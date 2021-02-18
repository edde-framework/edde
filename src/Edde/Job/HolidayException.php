<?php
declare(strict_types=1);

namespace Edde\Job;

/**
 * When there are no jobs to do, it's Holiday!
 */
class HolidayException extends JobException {
}
