<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IHostUrl;
	use Edde\Api\Url\UrlException;
	use Edde\Common\Url\Url;

	class HostUrl extends Url implements IHostUrl {
		/**
		 * default global environment based host url ($_SERVER,...)
		 *
		 * @return IHostUrl
		 * @throws UrlException
		 */
		static public function factory(): IHostUrl {
			return self::create(sprintf('%s://%s:%s', $_SERVER['REQUEST_SCHEME'], $_SERVER['SERVER_ADDR'], $_SERVER['SERVER_PORT']));
		}
	}
