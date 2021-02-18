<?php
declare(strict_types=1);

namespace Edde\Runtime;

interface IRuntime {
    /***
     * @return bool
     */
    public function isConsoleMode(): bool;

    /**
     * return argument list
     *
     * @return array
     *
     * @throws RuntimeException
     */
    public function getArguments(): array;
}
