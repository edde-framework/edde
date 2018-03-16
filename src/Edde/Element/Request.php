<?php
	declare(strict_types=1);
	namespace Edde\Element;

	class Request extends Element implements IRequest {
		public function __construct(string $service, string $method, string $uuid, array $parameters = []) {
			parent::__construct('request', $uuid, [
				'service'    => $service,
				'method'     => $method,
				'parameters' => $parameters,
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
		public function getParameters(): array {
			return $this->getAttribute('parameters', []);
		}
	}
