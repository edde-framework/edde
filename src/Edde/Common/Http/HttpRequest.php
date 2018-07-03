<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IHttpRequest;
	use Edde\Common\Converter\Content;

	class HttpRequest extends Request implements IHttpRequest {
		/**
		 * @var IHttpRequest
		 */
		static protected $httpRequest;

		static public function createHttpRequest(): IHttpRequest {
			self::$httpRequest ?: self::$httpRequest = new HttpRequest(RequestUrl::createRequestUrl(), HeaderList::createHeaderList(), CookieList::createCookieList());
			$input = fopen('php://input', 'r');
			if (empty($_POST) === false) {
				$content = new Content($_POST, 'post');
			} else if (fgetc($input) !== false) {
				$headerList = self::$httpRequest->getHeaderList();
				$contentType = $headerList->getContentType();
				$content = new Content('php://input', 'stream+' . $contentType->getMime());
			}
			fclose($input);
			isset($content) ? self::$httpRequest->setContent($content) : null;
			return self::$httpRequest;
		}
	}
