<?php
	declare(strict_types=1);

	namespace Edde\Api\Xml;

	trait LazyXmlExportTrait {
		/**
		 * @var IXmlExport
		 */
		protected $xmlExport;

		/**
		 * @param IXmlExport $xmlExport
		 */
		public function lazyXmlExport(IXmlExport $xmlExport) {
			$this->xmlExport = $xmlExport;
		}
	}
