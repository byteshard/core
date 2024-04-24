<?php

namespace byteShard\Internal\Authentication;

class AuthenticationResult
{
    public function __construct(private bool $success = false, private ?AuthenticationError $error = null)
    {

    }

    public function isSuccess(): bool
    {
        return $this->error === null && $this->success;
    }

    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    public function getError(): ?AuthenticationError
    {
        return $this->error;
    }

    public function setError(?AuthenticationError $error): void
    {
        $this->error = $error;
    }
}
