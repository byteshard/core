<?php

namespace byteShard\Utils;

use byteShard\Utils\Shell\ShellResult;
use Exception;

class Shell
{
    /**
     * @param string|array<string> $command
     * @throws Exception
     */
    public static function exec(string|array $command): ShellResult
    {
        $proc   = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);
        if ($proc === false) {
            throw new Exception('Cannot access shell');
        }
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        if ($stdout === false) {
            $stdout = '';
        }
        if ($stderr === false) {
            $stderr = '';
        }
        return new ShellResult(proc_close($proc), rtrim($stdout), rtrim($stderr));
    }
}
