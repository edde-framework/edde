<?php
declare(strict_types=1);

namespace Edde\Service\Job;

use Edde\Job\IJobManager;

trait JobManager {
    /** @var IJobManager */
    protected $jobManager;

    /**
     * @param IJobManager $jobManager
     */
    public function injectJobManager(IJobManager $jobManager): void {
        $this->jobManager = $jobManager;
    }
}
