<?php
	declare(strict_types=1);

	namespace Edde\Api\Xml\Inject;

	use Edde\Api\Xml\IXmlParser;

	/**
	 * Lazy xml parser dependency.
	 */
	trait XmlParser {
		/**
		 * @var IXmlParser
		 */
		protected $xmlParser;

		/**
		 * @param IXmlParser $xmlParser
		 */
		public function lazyXmlParser(IXmlParser $xmlParser) {
			$this->xmlParser = $xmlParser;
		}
	}
