<?php

namespace holybunch\shared\logs;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\IntrospectionProcessor;

/**
 * Class Logger
 * 
 * This class provides a singleton logger instance using Monolog library for logging purposes.
 */
class Logger
{
    private static ?MonologLogger $instance = null;

    /**
     * Build and return the logger instance.
     * 
     * @param string $root The root directory where log files will be stored.
     * @param Level $level The logging level (default is Level::Info).
     * @return MonologLogger The logger instance.
     */
    public static function build(string $root, Level $level = Level::Info): MonologLogger
    {
        if (!isset(self::$instance)) {
            self::$instance = new MonologLogger("logger");

            $handler = new StreamHandler(
                sprintf("%s/%s/log-%s-%s.log", $root, date("Y"), date("m"), date("d")),
                $level
            );
            $handler->setFormatter(new LineFormatter(
                "[%datetime%] [%level_name%]: %message% %context% \n",
                'Y-m-d H:i:s',
                false,
                true
            ));

            self::$instance->pushHandler($handler);
            self::$instance->pushHandler(new FirePHPHandler());
            self::$instance->pushProcessor(new IntrospectionProcessor(Level::Info, [], 1));
        }
        return  self::$instance;
    }
}
