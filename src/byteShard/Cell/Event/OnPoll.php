<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Cell\Event;

use byteShard\Action\Cell\ContinuePolling;
use byteShard\Enum\AccessType;
use byteShard\Internal\Event\Event;
use byteShard\Internal\Event\EventMigrationInterface;
use byteShard\Session;

class OnPoll extends Event implements EventMigrationInterface
{
    protected static string $event = 'onPoll';

    public function getClientArray(string $cellNonce): array
    {
        return ['onPoll' => ContinuePolling::getPollId($cellNonce)];
        $time = microtime();
        $encrypted = [
            'i' => $time,
            'a' => AccessType::RW,
            't' => 'poll'
        ];

        // the nonce should be unique per object, but we need to be able to recreate it for object access in actions.
        // The solution is to take a part of the stored nonce and add the object name, generate a md5 from this and use the first 24 characters
        // The nonce will be unique per client rendering as the cell nonce is recycled whenever content is reloaded.
        // That way we can manipulate objects of an existing client form, but we're also in compliance with security recommendations
        $nonce = substr(md5($cellNonce.$time), 0, 24);
        return ['onPoll' => Session::encrypt(json_encode($encrypted), $nonce)];
        //return ['onPoll' => Session::encrypt(json_encode(['i' => 'foo']), $cellNonce)];
    }
}
