<?php
declare(strict_types=1);

namespace Edde\Http;

use Edde\Url\IUrl;

/**
 * Low level implementation of HTTP request.
 */
interface IRequest extends IHttp {
    /**
     * @return IUrl
     */
    public function getUrl(): IUrl;

    /**
     * @return string
     */
    public function getMethod(): string;
}
