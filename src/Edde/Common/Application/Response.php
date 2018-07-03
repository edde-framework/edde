<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponse;
	use Edde\Common\AbstractObject;

	class Response extends AbstractObject implements IResponse {
		/**
		 * @var string
		 */
		protected $type;
		protected $response;

		/**
		 * @param string $type
		 * @param $response
		 */
		public function __construct(string $type = null, $response = null) {
			$this->type = $type;
			$this->response = $response;
		}

		public function getType(): string {
			return $this->type;
		}

		public function getResponse() {
			return $this->response;
		}
	}
