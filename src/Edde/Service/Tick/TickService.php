<?php
declare(strict_types=1);

namespace Edde\Service\Tick;

use Edde\Tick\ITickService;

trait TickService {
    /** @var ITickService */
    protected $tickService;

    /**
     * @param ITickService $tickService
     */
    public function lazyTickService(ITickService $tickService): void {
        $this->tickService = $tickService;
    }
}
