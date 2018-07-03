<?php
	declare(strict_types=1);

	namespace Edde\Api\Xml\Inject;

	use Edde\Api\Xml\IXmlExport;

	trait XmlExport {
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
