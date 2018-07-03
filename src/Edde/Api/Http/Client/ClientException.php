<?php
	declare(strict_types=1);

	namespace Edde\Api\Http\Client;

	use Edde\Api\EddeException;
	use Exception;

	class ClientException extends EddeException {
		/**
		 * @var IResponse
		 */
		protected $response;

		/**
		 * @param string         $message
		 * @param int            $code
		 * @param Exception|null $previous
		 * @param IResponse|null $response
		 */
		public function __construct($message = '', $code = 0, Exception $previous = null, IResponse $response = null) {
			parent::__construct($message, $code, $previous);
			$this->response = $response;
		}

		/**
		 * @return IResponse
		 */
		public function getResponse(): IResponse {
			return $this->response;
		}
	}
