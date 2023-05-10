<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Config;

use byteShard\Environment;
use byteShard\Internal\Config;
use Exception;

class ByteShard extends Environment
{
    private static ?ByteShard $instance = null;

    public static function getInstance(Config $config): ByteShard
    {
        if (self::$instance === null) {
            $class = get_called_class();
            if (class_exists('\\App\\Config\\ByteShard') && $class === self::class) {
                /**
                 * @noinspection PhpUndefinedNamespaceInspection
                 * @noinspection PhpUndefinedClassInspection
                 * @noinspection PhpFieldAssignmentTypeMismatchInspection
                 */
                $appConfig = new \App\Config\ByteShard($config);
                if ($appConfig instanceof ByteShard) {
                    self::$instance = $appConfig;
                } else {
                    self::$instance = new $class($config);
                }
            } else {
                self::$instance = new $class($config);
            }
        }
        return self::$instance;
    }

    /**
     * prevent the creation of an instance. Use getInstance() instead
     */
    protected function __construct(Config $config)
    {
        $this->construct($config);
    }

    /**
     * prevent the instance from being cloned
     */
    private function __clone()
    {
    }

    /**
     * prevent from being un-serialized
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception('Cannot un-serialize ByteShard');
    }
}
