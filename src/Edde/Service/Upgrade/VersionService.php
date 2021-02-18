<?php
declare(strict_types=1);

namespace Edde\Service\Upgrade;

use Edde\Upgrade\IVersionService;

trait VersionService {
    /** @var IVersionService */
    protected $versionService;

    /**
     * @param IVersionService $versionService
     */
    public function injectVersionService(IVersionService $versionService): void {
        $this->versionService = $versionService;
    }
}
