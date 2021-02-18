<?php
declare(strict_types=1);

namespace Edde\Log;

use Edde\Edde;

/**
 * Simple log record; holds record without any modifications.
 */
class Log extends Edde implements ILog {
    /** @var string */
    protected $log;

    /**
     * A blonde rings up an airline.
     * She asks, "How long are your flights from America to England?"
     * The woman on the other end of the phone says, "Just a minute..."
     * The blonde says, "Thanks!" and hangs up the phone.
     *
     * @param string $log
     */
    public function __construct($log) {
        $this->log = $log;
    }

    /** @inheritdoc */
    public function getLog() {
        return $this->log;
    }
}
