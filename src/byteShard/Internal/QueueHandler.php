<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

use byteShard\Database;
use byteShard\Exception;
use byteShard\Queueable;

class QueueHandler
{
    private string $queueId;
    public const RUNNING   = 'running';
    public const FAILED    = 'failed';
    public const FINISHED  = 'finished';
    public const EXCEPTION = 'exception';
    public const NO_CLASS  = 'classNotFound';

    /**
     * QueueHandler constructor.
     * @param string $queueId
     */
    public function __construct(string $queueId)
    {
        if (preg_match('/^[a-z]{2,10}$/', $queueId) !== 1) {
            Debug::debug('[QueueHandler] invalid queue Id. Only a-z and 2-10 charcters are allowed. Reverting to default.');
            $queueId = 'default';
        }
        Debug::debug('[QueueHandler] new instance created. Id: '.$queueId);
        $this->queueId = $queueId;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $queueItems = $this->getQueuedItems();
        if (!empty($queueItems)) {
            foreach ($queueItems as $queueId => $items) {
                $sessionFile = match ($queueId) {
                    'default' => rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'bs_queue_session',
                    default   => rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'bs_queue_session_'.$queueId,
                };
                // We use the session locking mechanism to make sure only one queue is running.
                // The next queue will automatically start once the session will be closed
                if (file_exists($sessionFile)) {
                    $sessionId = file_get_contents($sessionFile);
                    session_id($sessionId);
                    session_start();
                } else {
                    session_start();
                    file_put_contents($sessionFile, session_id());
                }
                $this->runQueue($items);
                session_write_close();
            }
            // all queues were running. Rerun this method, if no tries are left, it will exit
            $this->run();
        }
        // no items left in queue, return
    }

    /**
     * get all records with more than 0 tries left. Primary index is the queue id, second level array are all items of the queue id
     * Any items with 0 tries left will raise a debug message
     * @return array
     * @throws Exception
     */
    private function getQueuedItems(): array
    {
        $incompleteRecords   = Database::getArray('SELECT id, class, data, queue, tries FROM bs_queue WHERE tries < 1 ORDER BY createdOn');
        $incompleteRecordIds = [];
        foreach ($incompleteRecords as $incompleteRecord) {
            $incompleteRecordIds[] = $incompleteRecord->id;
        }
        if (!empty($incompleteRecordIds)) {
            Debug::debug('[QueueHandler] unfinished job(s) in queue (ids: '.implode(', ', $incompleteRecordIds).')');
        }
        $queueRecords = Database::getArray('SELECT id, class, data, queue, tries FROM bs_queue WHERE tries > 0 ORDER BY createdOn DESC');
        $queueItems   = [];
        foreach ($queueRecords as $queueRecord) {
            if ($this->checkState($queueRecord->id)) {
                if ($this->queueId === 'all' || $queueRecord->queue === $this->queueId) {
                    $queueItems[$queueRecord->queue][] = $queueRecord;
                }
            }
        }
        return $queueItems;
    }

    /**
     * @param array $queueItems
     * @throws Exception
     */
    private function runQueue(array $queueItems): void
    {
        Debug::debug('[QueueHandler] '.(count($queueItems) === 1 ? '1 item in queue' : count($queueItems).' items in queue'));
        foreach ($queueItems as $queueItem) {
            if (is_subclass_of($queueItem->class, Queueable::class)) {
                $className = $queueItem->class;
                $queueable = new $className(unserialize($queueItem->data));
                $this->runQueueable($queueable, (int)$queueItem->id);
            } else {
                Debug::error('[QueueHandler] Class '.$queueItem->class.' not found or no subclass of Queueable');
                $this->setState((int)$queueItem->id, self::NO_CLASS);
            }
        }
    }

    /**
     * @param Queueable $queueable
     * @param int $queueId
     * @throws Exception
     */
    private function runQueueable(Queueable $queueable, int $queueId): void
    {
        $data = $queueable->getData();

        // state is false if either: the record doesn't exist anymore, the number of tries is less than 1, the state is NO_CLASS||EXCEPTION||FINISHED
        $state = $this->checkState($queueId);
        if ($state === true) {
            $this->setState($queueId, self::RUNNING);
            try {
                $result = $queueable->run($data);
            } catch (Exception $e) {
                Debug::error('[QueueHandler] Exception in Job: '.$e->getMessage());
                $this->setState($queueId, self::EXCEPTION);
            }

            $this->reduceTries($queueId);

            if (isset($result) && is_bool($result)) {
                if ($result === true) {
                    $this->setState($queueId, self::FINISHED);
                    $queueable->successCallback($data);
                    Debug::info('[QueueHandler] Job finished successfully');
                    $this->removeEntryFromQueue($queueId);
                } else {
                    $this->setState($queueId, self::FAILED);
                    if ($this->getRetries($queueId) > 0) {
                        // no
                        Debug::error('[QueueHandler] Job failed, retrying');
                        $this->runQueueable($queueable, $queueId);
                    } else {
                        $queueable->failureCallback($data);
                        Debug::error('[QueueHandler] Job failed, retry limit reached');
                    }
                }
            } else {
                $queueable->failureCallback($data);
            }
        }
    }

    /**
     * @param int $id
     * @throws Exception
     */
    private function reduceTries(int $id): void
    {
        Debug::debug('[QueueHandler::'.$id.'] Reduce tries for ID');
        $record = Database::getSingle('SELECT tries FROM bs_queue WHERE id=:id', ['id' => $id]);
        if ($record !== null) {
            Debug::debug('[QueueHandler::'.$id.'] Record found for ID');
            Debug::debug('[QueueHandler::'.$id.'] Current number of tries: '.$record->tries);
            if ($record->tries > 0) {
                Database::update('UPDATE bs_queue SET tries=:tries WHERE id=:id', ['tries' => ((int)$record->tries) - 1, 'id' => $id]);
            }
        }
        $record = Database::getSingle('SELECT tries FROM bs_queue WHERE id=:id', ['id' => $id]);
        Debug::debug('[QueueHandler::'.$id.'] New number of tries: '.$record->tries);
    }

    /**
     * @param int $id
     * @return int
     * @throws Exception
     */
    private function getRetries(int $id): int
    {
        $record = Database::getSingle('SELECT tries FROM bs_queue WHERE id=:id', ['id' => $id]);
        if (!empty($record)) {
            return max($record->tries, 0);
        }
        return 0;
    }

    /**
     * @param int $id
     * @throws Exception
     */
    private function removeEntryFromQueue(int $id): void
    {
        Database::delete('DELETE FROM bs_queue WHERE id=:id', ['id' => $id]);
    }

    /**
     * @param int $id
     * @param string $state
     * @return bool
     * @throws Exception
     */
    private function setState(int $id, string $state): bool
    {
        return Database::update('UPDATE bs_queue SET jobState=:jobState WHERE id=:id', ['jobState' => $state, 'id' => $id]) !== 0;
    }

    /**
     * @param int $id
     * @return bool
     * @throws Exception
     */
    private function checkState(int $id): bool
    {
        $record = Database::getSingle('SELECT jobState, tries FROM bs_queue WHERE id=:id', ['id' => $id]);
        if ($record !== null) {
            if ($record->jobState === self::EXCEPTION || $record->jobState === self::NO_CLASS || $record->jobState === self::FINISHED || $record->tries < 1) {
                return false;
            }
            return true;
        }
        return false;
    }
}
