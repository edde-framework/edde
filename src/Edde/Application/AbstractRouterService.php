<?php
declare(strict_types=1);

namespace Edde\Application;

use Edde\Edde;
use Throwable;

abstract class AbstractRouterService extends Edde implements IRouterService {
    /** @var IRequest */
    protected $default;
    /** @var Throwable */
    protected $exception;

    /** @inheritdoc */
    public function default(IRequest $request): IRouterService {
        $this->default = $request;
        return $this;
    }

    /** @inheritdoc */
    public function hasException(): bool {
        return $this->exception !== null;
    }

    /** @inheritdoc */
    public function getException(): Throwable {
        return $this->exception;
    }
}
