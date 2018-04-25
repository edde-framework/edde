<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Edde;

	class Request extends Edde implements IRequest {
		/** @var string */
		protected $service;
		/** @var string */
		protected $method;
		/** @var array */
		protected $params;

		/**
		 * @param string $service
		 * @param string $method
		 * @param array  $params
		 */
		public function __construct(string $service, string $method, array $params = []) {
			$this->service = $service;
			$this->method = $method;
			$this->params = $params;
		}

		/** @inheritdoc */
		public function getService(): string {
			return $this->service;
		}

		/** @inheritdoc */
		public function getMethod(): string {
			return $this->method;
		}

		/** @inheritdoc */
		public function getParams(): array {
			return $this->params;
		}
	}
