<?php
declare(strict_types=1);

namespace Edde\Application;

use Edde\Configurable\IConfigurable;
use Edde\Runtime\RuntimeException;
use Edde\Url\UrlException;
use Throwable;

/**
 * Simplified request handling service: as it's not necessary to support
 * plenty of routers by default, this service is responsible for request creation,
 * thus also opening possibility to use classic routers (like others do).
 */
interface IRouterService extends IConfigurable {
    /**
     * set default request when router cannot determine it from runtime
     *
     * @param IRequest $request
     *
     * @return IRouterService
     */
    public function default(IRequest $request): IRouterService;

    /**
     * create (or get) an application request from current environment (http/cli; should be singleton instance)
     *
     * @return IRequest
     *
     * @throws RouterException if it's not possible to handle current request
     * @throws RuntimeException
     * @throws UrlException
     */
    public function request(): IRequest;

    /**
     * is there an exception caught during request creation?
     *
     * @return bool
     */
    public function hasException(): bool;

    /**
     * get an exception if any has been thrown
     *
     * @return Throwable
     */
    public function getException(): Throwable;
}
