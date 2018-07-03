<?php
	declare(strict_types=1);

	namespace Edde\Api\Client;

	use Edde\Api\EddeException;
	use Edde\Api\Http\IHttpResponse;
	use Exception;

	class ClientException extends EddeException {
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;

		/**
		 * @param string             $message
		 * @param int                $code
		 * @param Exception|null     $previous
		 * @param IHttpResponse|null $httpResponse
		 */
		public function __construct($message = "", $code = 0, Exception $previous = null, IHttpResponse $httpResponse = null) {
			parent::__construct($message, $code, $previous);
			$this->httpResponse = $httpResponse;
		}

		/**
		 * @return IHttpResponse
		 */
		public function getHttpResponse(): IHttpResponse {
			return $this->httpResponse;
		}
	}
