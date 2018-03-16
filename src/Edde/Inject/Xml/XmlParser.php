<?php
	declare(strict_types=1);
	namespace Edde\Inject\Xml;

	use Edde\Xml\IXmlParser;

	/**
	 * Lazy xml parser dependency.
	 */
	trait XmlParser {
		/**
		 * @var \Edde\Xml\IXmlParser
		 */
		protected $xmlParser;

		/**
		 * @param \Edde\Xml\IXmlParser $xmlParser
		 */
		public function lazyXmlParser(\Edde\Xml\IXmlParser $xmlParser) {
			$this->xmlParser = $xmlParser;
		}
	}
