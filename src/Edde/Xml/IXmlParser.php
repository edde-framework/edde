<?php
	declare(strict_types=1);
	namespace Edde\Xml;

	use Edde\Config\IConfigurable;
	use Edde\Exception\Xml\XmlParserException;
	use Edde\Resource\IResource;

	/**
	 * Event based xml parser.
	 */
	interface IXmlParser extends IConfigurable {
		/**
		 * parse the given string
		 *
		 * @param string      $string
		 * @param IXmlHandler $xmlHandler
		 *
		 * @return IXmlParser
		 *
		 * @throws XmlParserException
		 */
		public function string(string $string, IXmlHandler $xmlHandler): IXmlParser;

		/**
		 * shorthand for usage with files
		 *
		 * @param string      $file
		 * @param IXmlHandler $xmlHandler
		 *
		 * @return IXmlParser
		 *
		 * @throws XmlParserException
		 */
		public function file(string $file, IXmlHandler $xmlHandler): IXmlParser;

		/**
		 * parse the input stream and emit events to the given xml handler
		 *
		 * @param IResource   $resource
		 * @param IXmlHandler $xmlHandler
		 *
		 * @return IXmlParser
		 *
		 * @throws XmlParserException
		 */
		public function parse(IResource $resource, IXmlHandler $xmlHandler): IXmlParser;
	}