<?php
declare(strict_types=1);

namespace Edde\Service\Runtime;

use Edde\Runtime\IRuntime;

/**
 * Lazy runtime dependency.
 */
trait Runtime {
    /** @var IRuntime */
    protected $runtime;

    /**
     * @param IRuntime $runtime
     */
    public function injectRuntime(IRuntime $runtime) {
        $this->runtime = $runtime;
    }
}
