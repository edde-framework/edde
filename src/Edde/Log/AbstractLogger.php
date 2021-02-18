<?php
declare(strict_types=1);

namespace Edde\Log;

use Edde\Edde;
use Throwable;

/**
 * Common stuff for loggers.
 */
abstract class AbstractLogger extends Edde implements ILogger {
    /** @var string */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct(string $name = null) {
        $this->name = $name ?: static::class;
    }

    /** @inheritdoc */
    public function getName(): string {
        return $this->name;
    }

    /** @inheritdoc */
    public function log($log, array $tags = []): void {
        $this->record(new Log($log), $tags);
    }

    /** @inheritdoc */
    public function info(string $log, array $tags = []): void {
        $tags[] = __FUNCTION__;
        $this->log($log, $tags);
    }

    /** @inheritdoc */
    public function warning(string $log, array $tags = []): void {
        $tags[] = __FUNCTION__;
        $this->log($log, $tags);
    }

    /** @inheritdoc */
    public function error(string $log, array $tags = []): void {
        $tags[] = __FUNCTION__;
        $this->log($log, $tags);
    }

    /** @inheritdoc */
    public function critical(string $log, array $tags = []): void {
        $tags[] = __FUNCTION__;
        $this->log($log, $tags);
    }

    /** @inheritdoc */
    public function exception(Throwable $exception, array $tags = []): void {
        $tags[] = __FUNCTION__;
        $this->log($exception, $tags);
    }
}
