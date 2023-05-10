<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\ClientData;

use byteShard\Cell;
use byteShard\Internal\Struct\ClientData;
use byteShard\Internal\Struct\GetData;
use DateTime;
use DateTimeZone;

class ProcessedDataStructure
{
    private array         $data       = [];
    private array         $errors     = [];
    private array         $cellData   = [];
    private array         $gridRowIds = [];
    private ?DateTime     $clientQueryTime;
    private ?DateTimeZone $clientTimeZone;

    public function __construct(?DateTime $clientQueryTime, ?DateTimeZone $clientTimeZone)
    {
        $this->clientQueryTime = $clientQueryTime;
        $this->clientTimeZone  = $clientTimeZone;
    }

    public function addProcessedClientData(ProcessedClientData $clientData): void
    {
        if (empty($clientData->failedValidationMessages)) {
            $this->data[$clientData->id] = $clientData;
        } else {
            foreach ($clientData->failedValidationMessages as $message) {
                $this->errors[] = $message;
            }
        }
    }

    public function addGridRows(array $rowIds): void
    {
        $this->gridRowIds = $rowIds;
    }

    public function addProcessedGetCellData(string $cellId, ProcessedDataStructure $dataStructure): void
    {
        $cellName = trim(Cell::getContentCellName($cellId), '\\');
        if ($dataStructure->hasErrors()) {
            foreach ($dataStructure->getErrorMessages() as $message) {
                $this->errors[] = $message;
            }
        } else {
            $this->cellData[$cellName] = $dataStructure;
        }
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrorMessages(): array
    {
        return $this->errors;
    }

    /**
     * @return ProcessedClientData[]
     */
    public function getProcessedClientData(): array
    {
        return array_values($this->data);
    }

    public function getClientData(): ?ClientData
    {
        if (!empty($this->gridRowIds)) {
            return ClientData::createForGrid($this->clientQueryTime, $this->clientTimeZone, $this->gridRowIds, ...array_values($this->data));
        }
        return ClientData::createFromProcessedClientData($this->clientQueryTime, $this->clientTimeZone, ...array_values($this->data));
    }

    public function getGetCellData(): ?GetData
    {
        if (empty($this->cellData)) {
            return null;
        }
        $result = new GetData();
        foreach ($this->cellData as $cellId => $processedDataStructure) {
            $clientData = $processedDataStructure->getClientData();
            if ($clientData !== null) {
                $result->{$cellId} = $clientData;
            }
        }
        return $result;
    }
}