<?php
	declare(strict_types=1);
	namespace Edde\Element;

	class Request extends Element implements IRequest {
		public function __construct(string $service, string $method, string $uuid, array $params = []) {
			parent::__construct('request', $uuid, [
				'service' => $service,
				'method'  => $method,
				'params'  => $params,
			]);
		}

		/** @inheritdoc */
		public function getService(): string {
			return (string)$this->getAttribute('service');
		}

		/** @inheritdoc */
		public function getMethod(): string {
			return (string)$this->getAttribute('method');
		}

		/** @inheritdoc */
		public function getParams(): array {
			return $this->getAttribute('params', []);
		}
	}
