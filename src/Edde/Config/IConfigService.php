<?php
declare(strict_types=1);

namespace Edde\Config;

use Edde\Configurable\IConfigurable;

/**
 * Main purpose of this service is to provide application global configuration
 * base on simple sections and scalar values; this is not "classic" complex
 * configuration piece of shit like in other frameworks; main idea is to transport
 * environment variables (expecting Docker environment) to simple config, so an
 * application will not depend on env. variables directly.
 *
 * In general, config backend of this class could be everything, including ENVs.
 */
interface IConfigService extends IConfigurable {
    /**
     * @param string $name
     *
     * @return ISection
     *
     * @throws ConfigException
     */
    public function require(string $name): ISection;

    /**
     * @param string $name
     *
     * @return ISection
     *
     * @throws ConfigException
     */
    public function optional(string $name): ISection;
}
