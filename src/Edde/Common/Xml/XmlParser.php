<?php
	declare(strict_types=1);

	namespace Edde\Common\Xml;

	use Edde\Api\File\FileException;
	use Edde\Api\Iterator\IIterator;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Xml\Exception\XmlParserException;
	use Edde\Api\Xml\IXmlHandler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\File\File;
	use Edde\Common\Iterator\ChunkIterator;
	use Edde\Common\Iterator\Iterator;
	use Edde\Common\Object\Object;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Simple and fast event based xml parser.
	 */
	class XmlParser extends Object implements IXmlParser {
		const XML_TYPE_WARP = null;
		const XML_TYPE_OPENTAG = 1;
		const XML_TYPE_CLOSETAG = 2;
		const XML_TYPE_SHORTTAG = 4;
		const XML_TYPE_DOCTYPE = 8;
		const XML_TYPE_CDATA = 16;
		const XML_TYPE_COMMENT = 32;
		const XML_TYPE_OPEN_COMMENT = 64;
		const XML_TYPE_CLOSE_COMMENT = 128;
		const XML_TYPE_HEADER = 256;
		const XML_TYPE_CLOSE_HEADER = 512;

		/**
		 * @inheritdoc
		 * @throws FileException
		 * @throws \Edde\Api\Xml\Exception\XmlParserException
		 */
		public function file(string $file, IXmlHandler $xmlHandler): IXmlParser {
			return $this->parse(new File($file), $xmlHandler);
		}

		/**
		 * @inheritdoc
		 * @throws \Edde\Api\Xml\Exception\XmlParserException
		 */
		public function string(string $string, IXmlHandler $xmlHandler): IXmlParser {
			return $this->iterate(new Iterator(StringUtils::createIterator($string)), $xmlHandler);
		}

		/**
		 * @inheritdoc
		 * @throws \Edde\Api\Xml\Exception\XmlParserException
		 */
		public function parse(IResource $resource, IXmlHandler $xmlHandler): IXmlParser {
			$this->iterate(new Iterator(new ChunkIterator([
				StringUtils::class,
				'createIterator',
			], $resource->getIterator())), $xmlHandler);
			return $this;
		}

		/**
		 * @param IIterator   $iterator
		 * @param IXmlHandler $xmlHandler
		 *
		 * @return $this
		 * @throws \Edde\Api\Xml\Exception\XmlParserException
		 */
		protected function iterate(IIterator $iterator, IXmlHandler $xmlHandler) {
			$value = '';
			foreach ($iterator as $char) {
				/** @noinspection DegradedSwitchInspection */
				switch ($char) {
					case '<':
						if ($value !== '') {
							$xmlHandler->onTextEvent($value);
						}
						$this->parseTag($iterator->setContinue(), $xmlHandler);
						$value = '';
						break;
					default:
						$value .= $char;
				}
			}
			return $this;
		}

		/**
		 * @param IIterator   $iterator
		 * @param IXmlHandler $xmlHandler
		 *
		 * @throws XmlParserException
		 */
		protected function parseTag(IIterator $iterator, IXmlHandler $xmlHandler) {
			$last = null;
			$name = '';
			$attributeList = [];
			$type = self::XML_TYPE_WARP;
			foreach ($iterator as $char) {
				switch ($char) {
					case '<':
						$type = self::XML_TYPE_OPENTAG;
						$name = '';
						break;
					case '!':
						if ($last !== '<') {
							throw new \Edde\Api\Xml\Exception\XmlParserException(sprintf('Unexpected token [%s] while reading open tag.', $char));
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
							throw new \Edde\Api\Xml\Exception\XmlParserException(sprintf('Unexpected token [%s] while reading open tag.', $char));
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
								$this->parseComment($iterator->setContinue());
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
							$attributeList = $this->parseAttributes($iterator->setContinue());
							break;
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
			}
		}

		/**
		 * @param IIterator $iterator
		 */
		protected function parseComment(IIterator $iterator) {
			$type = self::XML_TYPE_COMMENT;
			$close = false;
			foreach ($iterator as $char) {
				switch ($char) {
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
			}
		}

		/**
		 * @param IIterator $iterator
		 *
		 * @return array
		 */
		protected function parseAttributes(IIterator $iterator) {
			$attributeList = [];
			foreach ($iterator as $char) {
				switch ($char) {
					case '/':
						$iterator->setSkipNext();
						return $attributeList;
					case '>':
						$iterator->setSkipNext();
						return $attributeList;
					case "\n":
					case "\t":
					case ' ':
						continue 2;
					default:
						/** @noinspection SlowArrayOperationsInLoopInspection */
						$attributeList = array_merge($attributeList, $this->parseAttribute($iterator->setContinue()));
				}
			}
			return $attributeList;
		}

		/**
		 * @param IIterator $iterator
		 *
		 * @return array
		 */
		protected function parseAttribute(IIterator $iterator) {
			$name = null;
			$open = false;
			$quote = null;
			$value = null;
			foreach ($iterator as $char) {
				switch ($char) {
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
							$iterator->next();
							$iterator->setSkipNext();
							return [$name => $value];
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
						continue 2;
					default:
						if ($open) {
							$value .= $char;
						} else {
							$name .= $char;
						}
				}
			}
			$iterator->setSkipNext();
			return [];
		}
	}
