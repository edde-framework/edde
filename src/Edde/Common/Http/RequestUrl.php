<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IRequestUrl;
	use Edde\Common\Url\Url;

	class RequestUrl extends Url implements IRequestUrl {
		/**
		 * @var IRequestUrl
		 */
		static protected $requestUrl;

		static public function createRequestUrl(): IRequestUrl {
			return self::$requestUrl ?: self::$requestUrl = self::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		}
	}
