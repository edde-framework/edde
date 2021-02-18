<?php
declare(strict_types=1);

namespace Edde\Log;

/**
 * Every log record must implement this interface.
 */
interface ILog {
    /**
     * compute target log item
     *
     * @return mixed
     */
    public function getLog();
}
