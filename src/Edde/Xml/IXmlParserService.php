<?php
	declare(strict_types=1);
	namespace Edde\Xml;

	use Edde\File\FileException;
	use Edde\File\IFile;

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
		 * @throws FileException
		 */
		public function file(string $file, IXmlHandler $xmlHandler): IXmlParserService;

		/**
		 * parse the input stream and emit events to the given xml handler
		 *
		 * @param IFile       $file
		 * @param IXmlHandler $xmlHandler
		 *
		 * @return IXmlParserService
		 *
		 * @throws XmlException
		 * @throws FileException
		 */
		public function parse(IFile $file, IXmlHandler $xmlHandler): IXmlParserService;
	}
