<?php
declare(strict_types=1);

namespace Edde\Log;

/**
 * Implementation of a log service.
 */
interface ILogService extends ILogger {
    /**
     * register the given log to the given set of tags
     *
     * @param ILogger $logger
     *
     * @return ILogService
     */
    public function registerLogger(ILogger $logger): ILogService;

    /**
     * do log to stdout (should use logger with proper tag)
     *
     * @param string $log
     * @param array  $tags
     *
     * @return ILogService
     */
    public function stdout(string $log, array $tags = []): ILogService;

    /**
     * do log to stderr (should use logger with proper tag)
     *
     * @param string $log
     * @param array  $tags
     *
     * @return ILogService
     */
    public function stderr(string $log, array $tags = []): ILogService;

    /**
     * disable logger with the given name
     *
     * @param string $name
     *
     * @return ILogService
     *
     * @throws LogException if the name does not exist
     */
    public function disable(string $name): ILogService;

    /**
     * @param string $name
     *
     * @return ILogService
     *
     * @throws LogException if the name does not exist
     */
    public function enable(string $name): ILogService;
}
