<?php
	declare(strict_types=1);
	namespace Edde\Inject\Xml;

	use Edde\Xml\IXmlParserService;

	/**
	 * Lazy xml parser dependency.
	 */
	trait XmlParserService {
		/**
		 * @var IXmlParserService
		 */
		protected $xmlParserService;

		/**
		 * @param IXmlParserService $xmlParserService
		 */
		public function lazyXmlParserService(IXmlParserService $xmlParserService): void {
			$this->xmlParserService = $xmlParserService;
		}
	}
