<?php
declare(strict_types=1);

namespace Edde\Service\Application;

use Edde\Application\IRouterService;

trait RouterService {
    /** @var IRouterService */
    protected $routerService;

    /**
     * @param IRouterService $routerService
     */
    public function injectRouterService(IRouterService $routerService): void {
        $this->routerService = $routerService;
    }
}
