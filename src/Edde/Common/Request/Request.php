<?php
	declare(strict_types=1);

	namespace Edde\Common\Request;

	use Edde\Common\Protocol\Element;

	class Request extends Element {
		public function __construct(string $request = null, array $data = [], string $id = null) {
			parent::__construct('request', $id);
			$this->setAttribute('request', $request);
			empty($data) === false ? $this->data($data) : null;
		}

		/**
		 * @inheritdoc
		 */
		public function getRequest(): string {
			return (string)$this->getAttribute('request');
		}
	}
