<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	class CsvFile extends File {
		/**
		 * @var string
		 */
		protected $delimiter = ';';

		public function setDelimiter(string $delimiter): CsvFile {
			$this->delimiter = $delimiter;
			return $this;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function read() {
			if (($line = fgetcsv($this->getHandle(), 0, $this->delimiter)) === false && $this->isAutoClose()) {
				$this->close();
			}
			return $line;
		}
	}
