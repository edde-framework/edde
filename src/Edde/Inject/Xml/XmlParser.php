<?php
	declare(strict_types=1);
	namespace Edde\Inject\Xml;

	use Edde\Xml\IXmlParser;

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
