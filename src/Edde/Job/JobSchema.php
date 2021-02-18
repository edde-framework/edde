<?php
declare(strict_types=1);

namespace Edde\Job;

use DateTime;
use Edde\Schema\UuidSchema;

interface JobSchema extends UuidSchema {
    const alias = true;

    /**
     * job state
     */
    public function state($default = self::STATE_ENQUEUED): int;

    /**
     * when a message should be executed
     */
    public function schedule(): DateTime;

    /**
     * message to be processed
     */
    public function message($type = 'json');

    /**
     * timestamp of last change; if state is 0, than job has been created by
     * "stamp" time and so on
     */
    public function stamp(): DateTime;

    public function result(): ?string;

    public function runtime(): ?float;
}
