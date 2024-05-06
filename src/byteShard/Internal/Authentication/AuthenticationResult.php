<?php

namespace byteShard\Internal\Authentication;

class AuthenticationResult
{
    public function __construct(private bool $success = false, private ?AuthenticationAction $action = null)
    {

    }

    public function isSuccess(): bool
    {
        return $this->action === null && $this->success;
    }

    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    public function getAction(): ?AuthenticationAction
    {
        return $this->action;
    }

    public function setAction(?AuthenticationAction $action): void
    {
        $this->action = $action;
    }
}
