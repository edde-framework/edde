<?php
	declare(strict_types=1);
	namespace Edde\Xml;

	use Edde\Io\IResource;

	/**
	 * Event based xml parser.
	 */
	interface IXmlParserService {
		/**
		 * parse the given string
		 *
		 * @param string      $string
		 * @param IXmlHandler $xmlHandler
		 *
		 * @return IXmlParserService
		 *
		 * @throws XmlException
		 */
		public function string(string $string, IXmlHandler $xmlHandler): IXmlParserService;

		/**
		 * shorthand for usage with files
		 *
		 * @param string      $file
		 * @param IXmlHandler $xmlHandler
		 *
		 * @return IXmlParserService
		 *
		 * @throws XmlException
		 */
		public function file(string $file, IXmlHandler $xmlHandler): IXmlParserService;

		/**
		 * parse the input stream and emit events to the given xml handler
		 *
		 * @param IResource   $resource
		 * @param IXmlHandler $xmlHandler
		 *
		 * @return IXmlParserService
		 *
		 * @throws XmlException
		 */
		public function parse(IResource $resource, IXmlHandler $xmlHandler): IXmlParserService;
	}
