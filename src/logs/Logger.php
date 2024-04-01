<?php

namespace holybunch\shared\logs;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Level;
use Monolog\Processor\IntrospectionProcessor;

class Logger extends \Monolog\Logger
{
    private const FILE_FORMAT = "%s/%s/log-%s-%s.log";

    private function __construct(string $name,)
    {
        parent::__construct($name);
    }

    private static $instance = null;
    public static function build(string $root, Level $level = Level::Info): self
    {
        if (self::$instance === null) {
            self::$instance = new self("logger");

            $handler = new StreamHandler(
                sprintf(self::FILE_FORMAT, $root, date("Y"), date("m"), date("d")),
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
            self::$instance->pushProcessor(new IntrospectionProcessor(Level::Info, array(), 1));
        }
        return  self::$instance;
    }
}
