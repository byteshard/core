<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal;

use byteShard\Enum\LogLevel;
use byteShard\Enum\LogLocation;
use byteShard\Environment;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ByteShard
{
    protected Config        $config;
    protected ?ErrorHandler $errorHandler  = null;
    private ?StreamHandler  $streamHandler = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Initialize byteShard
     * @return Environment
     */
    public function init(): Environment
    {
        ini_set('zlib.output_compression', 'On');
        ini_set('zlib.output_compression_level', 1);
        ini_set('max_execution_time', '600');

        $this->errorHandler = new ErrorHandler(
            $this->config->getLogPath(),
            trim($this->config->getUrl(), '/').'/'.trim($this->config->getUrlContext(), '/'),
            ErrorHandler::RESULT_OBJECT_HTML,
            $this->config->getLogLocation()
        );

        // create a stream handler and two log channels and pass them to the error handler
        if ($this->config->getLogLocation() === LogLocation::STDERR) {
            $this->streamHandler = new StreamHandler('php://stderr', LogLevel::getMonologLevel($this->config->getLogLevel()));
        } else {
            $this->streamHandler = new StreamHandler($this->config->getLogFilePath(), LogLevel::getMonologLevel($this->config->getLogLevel()));
        }
        $this->streamHandler->setFormatter(new LineFormatter(null, null, false, true));
        $bs_logger = new Logger('byteShard');
        $bs_logger->pushHandler($this->streamHandler);
        $default_logger = new Logger($this->config->getLogChannelName());
        $default_logger->pushHandler($this->streamHandler);
        $this->errorHandler->addLogger('byteShard', $bs_logger);
        $this->errorHandler->addLogger('default', $default_logger);
        Debug::addLogger('byteShard', $bs_logger);
        Debug::addLogger('default', $default_logger);
        return \byteShard\Config\ByteShard::getInstance($this->config);
    }

    public function getErrorHandler(): ?ErrorHandler
    {
        return $this->errorHandler;
    }

    public function setLogFormatter(FormatterInterface $formatter): self
    {
        $this->streamHandler->setFormatter($formatter);
        return $this;
    }
}