<?php
declare(strict_types=1);

namespace Edde\Upgrade;

use Edde\Configurable\IConfigurable;
use Edde\Storage\IEntity;
use Generator;

interface IVersionService extends IConfigurable {
    /**
     * return current version of the application; if null, the application is in zero-state (probably
     * not ready to be used)
     *
     * @return null|string
     */
    public function getVersion(): ?string;

    /**
     * given version will be current version of the application
     *
     * @param string $version
     *
     * @return IVersionService
     */
    public function update(string $version): IVersionService;

    /**
     * return a generator of installed versions (upgrades)
     *
     * @return Generator|IEntity[]
     */
    public function getCollection(): Generator;
}
