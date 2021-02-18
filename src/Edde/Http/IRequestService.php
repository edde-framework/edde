<?php
declare(strict_types=1);

namespace Edde\Http;

use Edde\Configurable\IConfigurable;
use Edde\Content\IContent;
use Edde\Url\IUrl;
use Edde\Url\UrlException;

interface IRequestService extends IConfigurable {
    /**
     * return a singleton instance representing current http request
     *
     * @return IRequest
     *
     * @throws UrlException
     */
    public function getRequest(): IRequest;

    /**
     * return content from a request
     *
     * @return IContent
     *
     * @throws EmptyBodyException
     * @throws UrlException
     */
    public function getContent(): IContent;

    /**
     * get current request url
     *
     * @return IUrl
     *
     * @throws UrlException
     */
    public function getUrl(): IUrl;

    /**
     * return current uppercase http method
     *
     * @return string
     *
     * @throws UrlException
     */
    public function getMethod(): string;

    /**
     * get headers of the current request
     *
     * @return IHeaders
     *
     * @throws UrlException
     */
    public function getHeaders(): IHeaders;
}
