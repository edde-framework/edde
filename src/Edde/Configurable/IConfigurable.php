<?php
declare(strict_types=1);

namespace Edde\Configurable;

/**
 * Marker interface for classes supporting external configuration (by a Configurator interface).
 */
interface IConfigurable {
    /**
     * register set of config handlers
     *
     * @param IConfigurator[] $configurators
     *
     * @return $this
     */
    public function setConfigurators(array $configurators);

    /**
     * this method should be called after all dependencies are
     * available; also there should NOT be any heavy computations, only
     * lightweight simple stuff
     *
     * @return $this
     */
    public function init();

    /**
     * do any heavy computations; after this object is usually not serializable
     *
     * @return $this
     */
    public function setup();

    /**
     * has the configurable already been configured?
     *
     * @return bool
     */
    public function isSetup(): bool;
}
