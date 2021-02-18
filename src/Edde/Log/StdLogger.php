<?php
declare(strict_types=1);

namespace Edde\Log;

use function fwrite;
use function in_array;
use const STDERR;
use const STDOUT;

/**
 * Standard output/error logger.
 */
class StdLogger extends AbstractLogger {
    /** @inheritdoc */
    public function record(ILog $log, array $tags = []): void {
        if (in_array('stdout', $tags)) {
            fwrite(STDOUT, $log->getLog());
        }
        if (in_array('stderr', $tags)) {
            fwrite(STDERR, $log->getLog());
        }
    }
}
