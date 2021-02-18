<?php
declare(strict_types=1);

namespace Edde\Log;

use function array_unique;
use function sprintf;

/**
 * Default implementation of log service.
 */
class LogService extends AbstractLogger implements ILogService {
    /** @var ILogger[] */
    protected $loggers = [];
    /** @var ILogger[] */
    protected $disabled = [];

    /** @inheritdoc */
    public function registerLogger(ILogger $logger): ILogService {
        $this->loggers[$logger->getName()] = $logger;
        return $this;
    }

    /** @inheritdoc */
    public function stdout(string $log, array $tags = []): ILogService {
        $tags[] = __FUNCTION__;
        $this->log($log, $tags);
        return $this;
    }

    /** @inheritdoc */
    public function stderr(string $log, array $tags = []): ILogService {
        $tags[] = __FUNCTION__;
        $this->log($log, $tags);
        return $this;
    }

    /** @inheritdoc */
    public function disable(string $name): ILogService {
        if (isset($this->loggers[$name]) === false) {
            throw new LogException(sprintf('Cannot disable unknown logger [%s].', $name));
        }
        $this->disabled[$name] = $this->loggers[$name];
        unset($this->loggers[$name]);
        return $this;
    }

    /** @inheritdoc */
    public function enable(string $name): ILogService {
        if (isset($this->disabled[$name]) === false) {
            throw new LogException(sprintf('Cannot enable unknown logger [%s].', $name));
        }
        $this->loggers[$name] = $this->disabled[$name];
        unset($this->disabled[$name]);
        return $this;
    }

    /** @inheritdoc */
    public function record(ILog $log, array $tags = []): void {
        $tags = array_unique($tags ?: []);
        foreach ($this->loggers as $logger) {
            $logger->record($log, $tags);
        }
    }
}
