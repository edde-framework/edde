<?php
	declare(strict_types = 1);

	namespace Edde\Api\Xml;

	/**
	 * Lazy xml parser dependency.
	 */
	trait LazyXmlParserTrait {
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
