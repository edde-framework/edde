<?php
declare(strict_types=1);

namespace Edde\Upgrades;

use Edde\Job\JobManagerSchema;
use Edde\Storage\Entity;
use Edde\Upgrade\AbstractUpgrade;

class ZeroJobManagerUpgrade extends AbstractUpgrade {
    /** @inheritdoc */
    public function getVersion(): string {
        return 'zero-job-manager';
    }

    /** @inheritdoc */
    public function upgrade(): void {
        $this->storage->insert(new Entity(JobManagerSchema::class, ['paused' => false]));
    }
}
