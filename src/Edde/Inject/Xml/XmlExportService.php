<?php
	declare(strict_types=1);
	namespace Edde\Inject\Xml;

	use Edde\Xml\IXmlExportService;

	trait XmlExportService {
		/**
		 * @var IXmlExportService
		 */
		protected $xmlExportService;

		/**
		 * @param IXmlExportService $xmlExportService
		 */
		public function lazyXmlExportService(IXmlExportService $xmlExportService): void {
			$this->xmlExportService = $xmlExportService;
		}
	}
