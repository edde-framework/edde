<?php
	declare(strict_types=1);
	namespace Edde\Service\Xml;

	use Edde\Xml\IXmlParserService;

	/**
	 * Lazy xml parser dependency.
	 */
	trait XmlParserService {
		/** @var IXmlParserService */
		protected $xmlParserService;

		/**
		 * @param IXmlParserService $xmlParserService
		 */
		public function injectXmlParserService(IXmlParserService $xmlParserService): void {
			$this->xmlParserService = $xmlParserService;
		}
	}
