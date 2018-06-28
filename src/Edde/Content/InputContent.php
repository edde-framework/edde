<?php
	declare(strict_types=1);
	namespace Edde\Content;

	/**
	 * Input content represent's php input stream.
	 */
	class InputContent extends Content {
		public function __construct(string $type) {
			parent::__construct('php://input', $type);
		}

		/** @inheritdoc */
		public function getContent() {
			return file_get_contents($this->content);
		}
	}
