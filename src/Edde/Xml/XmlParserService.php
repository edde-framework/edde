<?php
	declare(strict_types=1);
	namespace Edde\Xml;

	use Edde\Edde;
	use Edde\File\File;
	use Edde\File\IFile;
	use Edde\Service\Utils\StringUtils;
	use Iterator;

	/**
	 * Simple and fast event based xml parser.
	 */
	class XmlParserService extends Edde implements IXmlParserService {
		use StringUtils;
		protected const XML_TYPE_WARP = null;
		protected const XML_TYPE_OPENTAG = 1;
		protected const XML_TYPE_CLOSETAG = 2;
		protected const XML_TYPE_SHORTTAG = 4;
		protected const XML_TYPE_DOCTYPE = 8;
		protected const XML_TYPE_CDATA = 16;
		protected const XML_TYPE_COMMENT = 32;
		protected const XML_TYPE_OPEN_COMMENT = 64;
		protected const XML_TYPE_CLOSE_COMMENT = 128;
		protected const XML_TYPE_HEADER = 256;
		protected const XML_TYPE_CLOSE_HEADER = 512;

		/** @inheritdoc */
		public function string(string $string, IXmlHandler $xmlHandler): IXmlParserService {
			return $this->iterate($this->stringUtils->createIterator($string), $xmlHandler);
		}

		/** @inheritdoc */
		public function file(string $file, IXmlHandler $xmlHandler): IXmlParserService {
			return $this->parse(new File($file), $xmlHandler);
		}

		/** @inheritdoc */
		public function parse(IFile $file, IXmlHandler $xmlHandler): IXmlParserService {
			/**
			 * this interesting piece of things is reading line based data from $resource and
			 * streaming character per character to the iterator
			 */
			try {
				$file->open('r');
				$this->iterate((function (IFile $file) {
					foreach ($file as $line) {
						yield from $this->stringUtils->createIterator($line);
					}
				})($file), $xmlHandler);
			} finally {
				$file->close();
			}
			return $this;
		}

		/**
		 * @param Iterator    $iterator
		 * @param IXmlHandler $xmlHandler
		 *
		 * @return $this
		 *
		 * @throws XmlException
		 */
		protected function iterate(Iterator $iterator, IXmlHandler $xmlHandler) {
			$value = '';
			$iterator->rewind();
			while ($iterator->valid()) {
				/**
				 * switch is here intentionally to force same "language" in whole file
				 */
				switch ($char = $iterator->current()) {
					case '<':
						if ($value !== '') {
							$xmlHandler->onTextEvent($value);
						}
						$this->parseTag($iterator, $xmlHandler);
						$value = '';
						break;
					default:
						$value .= $char;
				}
				$iterator->next();
			}
			return $this;
		}

		/**
		 * @param Iterator    $iterator
		 * @param IXmlHandler $xmlHandler
		 *
		 * @throws XmlException
		 */
		protected function parseTag(Iterator $iterator, IXmlHandler $xmlHandler) {
			$last = null;
			$name = '';
			$attributeList = [];
			$type = self::XML_TYPE_WARP;
			while ($iterator->valid()) {
				switch ($char = $iterator->current()) {
					case '<':
						$type = self::XML_TYPE_OPENTAG;
						$name = '';
						break;
					case '!':
						if ($last !== '<') {
							throw new XmlException(sprintf('Unexpected token [%s] while reading open tag.', $char));
						}
						$type = self::XML_TYPE_DOCTYPE;
						$name .= $char;
						break;
					case '?':
						if ($type === self::XML_TYPE_HEADER) {
							$type = self::XML_TYPE_CLOSE_HEADER;
							break;
						}
						if ($last !== '<') {
							throw new XmlException(sprintf('Unexpected token [%s] while reading open tag.', $char));
						}
						$type = self::XML_TYPE_HEADER;
						break;
					case '-':
						switch ($type) {
							case self::XML_TYPE_DOCTYPE:
								$type = self::XML_TYPE_OPEN_COMMENT;
								break;
							case self::XML_TYPE_OPEN_COMMENT:
								$iterator->next();
								$this->parseComment($iterator);
								$name = null;
								break;
							default:
								$name .= $char;
						}
						break;
					case '/':
						$type = ($last !== '<' ? self::XML_TYPE_SHORTTAG : self::XML_TYPE_CLOSETAG);
						break;
					case "\n":
					case ' ':
						if ($type === self::XML_TYPE_OPENTAG) {
							$attributeList = $this->parseAttributes($iterator);
							continue 2;
						}
						$name .= $char;
						break;
					case '>':
						switch ($type) {
							case self::XML_TYPE_DOCTYPE:
								$xmlHandler->onDocTypeEvent($name);
								break;
							case self::XML_TYPE_OPENTAG:
								$xmlHandler->onOpenTagEvent($name, $attributeList);
								break;
							case self::XML_TYPE_SHORTTAG:
								$xmlHandler->onShortTagEvent($name, $attributeList);
								break;
							case self::XML_TYPE_CLOSETAG:
								$xmlHandler->onCloseTagEvent($name);
								break;
							case self::XML_TYPE_CLOSE_HEADER:
								$xmlHandler->onHeaderEvent($name);
								break;
						}
						return;
					default:
						$name .= $char;
				}
				$last = $char;
				$iterator->next();
			}
		}

		/**
		 * @param Iterator $iterator
		 *
		 * @throws XmlException
		 */
		protected function parseComment(Iterator $iterator) {
			$type = self::XML_TYPE_COMMENT;
			$close = false;
			while ($iterator->valid()) {
				switch ($char = $iterator->current()) {
					case '-':
						switch ($type) {
							case self::XML_TYPE_COMMENT:
								$type = self::XML_TYPE_CLOSE_COMMENT;
								break;
							case self::XML_TYPE_CLOSE_COMMENT:
								$close = true;
								break;
						}
						break;
					case '>':
						if ($close) {
							return;
						}
						break;
					default:
						$close = false;
						$type = self::XML_TYPE_COMMENT;
				}
				$iterator->next();
			}
			throw new XmlException('Parser ended during digging through comment. The God will kill one cute kitten now!');
		}

		/**
		 * @param Iterator $iterator
		 *
		 * @return array
		 * @throws XmlException
		 */
		protected function parseAttributes(Iterator $iterator) {
			$attributeList = [];
			while ($iterator->valid()) {
				switch ($char = $iterator->current()) {
					case '/':
					case '>':
						return $attributeList;
					case "\n":
					case "\t":
					case ' ':
						break;
					default:
						if ($attribute = $this->parseAttribute($iterator)) {
							$attributeList[$attribute[0]] = $attribute[1];
						}
				}
				$iterator->next();
			}
			throw new XmlException('Parser ended when eating attribute list. Something is not so good here!');
		}

		/**
		 * @param Iterator $iterator
		 *
		 * @return array
		 * @throws XmlException
		 */
		protected function parseAttribute(Iterator $iterator) {
			$name = null;
			$open = false;
			$quote = null;
			$value = null;
			while ($iterator->valid()) {
				switch ($char = $iterator->current()) {
					case '=':
						if ($open !== true) {
							$open = true;
							break;
						}
						$value .= $char;
						break;
					case '"':
					case "'":
						if ($char === $quote) {
							return [
								$name,
								$value,
							];
						}
						if ($quote !== null) {
							$value .= $char;
							break;
						}
						$quote = $char;
						break;
					case "\t":
					case "\n":
					case ' ':
						if ($open) {
							$value .= $char;
						}
						$iterator->next();
						continue 2;
					default:
						if ($open) {
							$value .= $char;
						} else {
							$name .= $char;
						}
				}
				$iterator->next();
			}
			throw new XmlException('Parser when parsing an attribute. That is quite strange!');
		}
	}
