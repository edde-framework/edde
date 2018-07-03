<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Common\Converter\AbstractConverter;

	class DomDocumentConverter extends AbstractConverter {
		public function __construct() {
			$this->register([
				'text/html',
				'text/xml',
				'applicaiton/xml',
				'xml',
			], \DOMDocument::class);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 */
		public function convert($convert, string $source, string $target, string $mime) {
			$this->unsupported($convert, $target, is_string($convert));
			switch ($target) {
				case \DOMDocument::class:
					$domDocument = new \DOMDocument();
					$domDocument->preserveWhiteSpace = false;
					switch ($source) {
						case 'text/html':
							$domDocument->loadHTML($convert);
							break;
						case 'xml':
						case 'text/xml':
						case 'application/xml':
							$domDocument->loadXML($convert);
							break;
						default:
							$this->exception($source, $target);
					}
					return $domDocument;
			}
			$this->exception($source, $target);
		}
	}
