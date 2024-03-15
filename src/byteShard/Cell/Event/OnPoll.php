<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Cell\Event;

use byteShard\Action\Cell\ContinuePolling;
use byteShard\Event\OnPollInterface;
use byteShard\Internal\Event\Event;
use byteShard\Internal\Event\EventMigrationInterface;
use byteShard\Internal\Event\ImplicitEventInterface;

class OnPoll extends Event implements EventMigrationInterface, ImplicitEventInterface
{
    protected static string $event = 'onPoll';

    public function getImplicitInterfaceClass(): string {
        return OnPollInterface::class;
    }

    public function getClientArray(string $cellNonce): array
    {
        return ['onPoll' => ContinuePolling::getPollId($cellNonce)];
    }
}
