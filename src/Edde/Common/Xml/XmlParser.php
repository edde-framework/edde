<?php
	declare(strict_types=1);
	namespace Edde\Common\Xml;

		use Edde\Api\Resource\IResource;
		use Edde\Api\Xml\Exception\XmlParserException;
		use Edde\Api\Xml\IXmlHandler;
		use Edde\Api\Xml\IXmlParser;
		use Edde\Common\File\File;
		use Edde\Common\Iterator\ChunkIterator;
		use Edde\Common\Iterator\Iterator;
		use Edde\Common\Iterator\StringIterator;
		use Edde\Common\Object\Object;

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
			 */
			public function file(string $file, IXmlHandler $xmlHandler): IXmlParser {
				return $this->parse(new File($file), $xmlHandler);
			}

			/**
			 * @inheritdoc
			 */
			public function string(string $string, IXmlHandler $xmlHandler): IXmlParser {
				return $this->iterate(new Iterator((new StringIterator($string))->getIterator()), $xmlHandler);
			}

			/**
			 * @inheritdoc
			 * @throws XmlParserException
			 */
			public function parse(IResource $resource, IXmlHandler $xmlHandler): IXmlParser {
				$this->iterate(new Iterator(new ChunkIterator(function (string $string) {
					return (new StringIterator($string))->getIterator();
				}, $resource->getIterator())), $xmlHandler);
				return $this;
			}

			/**
			 * @param Iterator    $iterator
			 * @param IXmlHandler $xmlHandler
			 *
			 * @return $this
			 * @throws XmlParserException
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
			 * @param \Iterator   $iterator
			 * @param IXmlHandler $xmlHandler
			 *
			 * @throws XmlParserException
			 */
			protected function parseTag(\Iterator $iterator, IXmlHandler $xmlHandler) {
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
								throw new XmlParserException(sprintf('Unexpected token [%s] while reading open tag.', $char));
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
								throw new XmlParserException(sprintf('Unexpected token [%s] while reading open tag.', $char));
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

			protected function parseComment(\Iterator $iterator) {
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
			}

			/**
			 * @param \Iterator $iterator
			 *
			 * @return array
			 */
			protected function parseAttributes(\Iterator $iterator) {
				$attributeList = [];
				while ($iterator->valid()) {
					switch ($char = $iterator->current()) {
						case '/':
						case '>':
							return $attributeList;
						case "\n":
						case "\t":
						case ' ':
							$iterator->next();
							continue 2;
						default:
							$attributeList = array_merge($attributeList, $this->parseAttribute($iterator));
					}
					$iterator->next();
				}
				return $attributeList;
			}

			/**
			 * @param \Iterator $iterator
			 *
			 * @return array
			 */
			protected function parseAttribute(\Iterator $iterator) {
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
				$iterator->setSkipNext();
				return [];
			}
		}
