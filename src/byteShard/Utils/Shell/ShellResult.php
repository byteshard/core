<?php

namespace byteShard\Utils\Shell;

class ShellResult
{
    public function __construct(
        private readonly int    $exitCode,
        private readonly string $stdOut,
        private readonly string $stdErr)
    {
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    public function getStdOut(): string
    {
        return $this->stdOut;
    }

    public function getStdErr(): string
    {
        return $this->stdErr;
    }
}