<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\File\File;

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

		/** @inheritdoc */
		public function getIterator() {
			$file = new File($this->content);
			try {
				$file->open('r');
				yield from $file;
			} finally {
				$file->close();
			}
		}
	}
