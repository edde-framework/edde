<?php
declare(strict_types=1);

namespace Edde\Service\Hydrator;

use Edde\Hydrator\IHydratorManager;

trait HydratorManager {
    /** @var IHydratorManager */
    protected $hydratorManager;

    /**
     * @param IHydratorManager $hydratorManager
     */
    public function injectHydratorManager(IHydratorManager $hydratorManager): void {
        $this->hydratorManager = $hydratorManager;
    }
}
