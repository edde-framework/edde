<?php
	declare(strict_types=1);
	namespace Edde\Inject\Xml;

	use Edde\Xml\IXmlExport;

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
