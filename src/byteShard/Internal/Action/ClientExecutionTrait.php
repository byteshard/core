<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Action;

trait ClientExecutionTrait
{
    private bool $executeOnClient = false;
    
    /**
     * @API
     * action will be executed on the client without sending an event to the server
     */
    public function setClientExecution(bool $bool = true): self
    {
        $this->executeOnClient = $bool;
        return $this;
    }

    /**
     * @return bool
     */
    public function getClientExecution(): bool
    {
        return $this->executeOnClient;
    }
}
