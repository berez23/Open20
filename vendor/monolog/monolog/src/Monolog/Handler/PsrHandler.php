<?php

/*
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 */

namespace Monolog\Handler;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Proxies log messages to an existing PSR-3 compliant logger.
 *
 */
class PsrHandler extends AbstractHandler
{
    /**
     * PSR-3 compliant logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger The underlying PSR-3 compliant logger to which messages will be proxied
     * @param int             $level  The minimum logging level at which this handler will be triggered
     * @param bool            $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(LoggerInterface $logger, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(array $record)
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        $this->logger->log(strtolower($record['level_name']), $record['message'], $record['context']);

        return false === $this->bubble;
    }
}
