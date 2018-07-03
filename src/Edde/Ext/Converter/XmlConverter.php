<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\Inject\ConverterManager;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Xml\Exception\XmlParserException;
	use Edde\Api\Xml\Inject\XmlExport;
	use Edde\Api\Xml\Inject\XmlParser;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Converter\Content;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Xml\XmlNodeHandler;

	/**
	 * Xml string sourece to "something" converter.
	 */
	class XmlConverter extends AbstractConverter {
		use ConverterManager;
		use XmlParser;
		use XmlExport;

		/**
		 * Only 3 things that are infinite
		 * 1.Human Stupidity
		 * 2.Universe
		 * 3.WinRar Trial
		 */
		public function __construct() {
			$this->register([
				'text/xml',
				'application/xml',
				'application/xhtml+xml',
				'xml',
				'string',
			], INode::class);
			$this->register([
				'text/xml',
				'application/xml',
				'application/xhtml+xml',
				'xml',
				'string',
			], [
				'*/*',
				'text/xml',
			]);
			$this->register(\stdClass::class, [
				'text/xml',
				'application/xml',
			]);
			$this->register(INode::class, [
				'text/xml',
				'application/xml',
			]);
		}

		/**
		 * @inheritdoc
		 * @throws XmlParserException
		 * @throws ConverterException
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			$this->unsupported($content, $target, $content instanceof IResource || is_string($content) || $content instanceof \stdClass || $content instanceof INode);
			try {
				switch ($mime) {
					case \stdClass::class:
						return new Content($this->xmlExport->string(NodeIterator::recursive($this->converterManager->convert($content, \stdClass::class, [INode::class])
							->convert()
							->getContent(), true)), $target);
						break;
					case INode::class:
						return new Content($this->xmlExport->string(NodeIterator::recursive($content, true)), $target);
						break;
					default:
						switch ($target) {
							case INode::class:
								$this->xmlParser->{is_string($content) ? 'string' : 'parse'}($content, $handler = new XmlNodeHandler());
								return new Content($handler->getNode(), INode::class);
							case 'application/xml':
							case 'text/xml':
							case '*/*':
								return new Content($content, 'application/xml');
						}
				}
			} catch (XmlParserException $e) {
				throw new XmlParserException(sprintf('Cannot handle resource [%s]: %s', (string)$content->getUrl(), $e->getMessage()), 0, $e);
			}
			return $this->exception($mime, $target);
		}

		/**
		 * @param IResource  $resource
		 * @param INode|null $root
		 *
		 * @return INode
		 * @throws XmlParserException
		 */
		public function handle(IResource $resource, INode $root = null): INode {
			try {
				$this->xmlParser->parse($resource, $handler = new XmlNodeHandler());
				$node = $handler->getNode();
				if ($root !== null) {
					$root->setNodeList($node->getNodeList(), true);
				}
				return $root ?: $node;
			} catch (XmlParserException $e) {
				throw new XmlParserException(sprintf('Cannot handle resource [%s]: %s', (string)$resource->getUrl(), $e->getMessage()), 0, $e);
			}
		}
	}
