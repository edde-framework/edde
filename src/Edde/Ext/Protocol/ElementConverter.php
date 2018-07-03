<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\Inject\ConverterManager;
	use Edde\Api\Node\INode;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Converter\Content;
	use Edde\Common\Node\NodeUtils;
	use Edde\Common\Protocol\Element;

	class ElementConverter extends AbstractConverter {
		use ConverterManager;

		public function __construct() {
			$this->register(IElement::class, [
				'stream+application/json',
				'application/json',
				'text/json',
				'text/xml',
				'*/*',
			]);
			$this->register([
				'stream+application/json',
				'application/json',
				'text/json',
				'post',
			], IElement::class);
		}

		/**
		 * @inheritdoc
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			switch ($target) {
				case IElement::class:
					switch ($mime) {
						case 'post':
							$this->unsupported($content, $target, is_array($content));
							return new Content($this->converterManager->convert(json_encode($content), 'application/json', [IElement::class])
								->convert()
								->getContent(), IElement::class);
						default:
							$this->unsupported($content, $target, is_string($content));
							return new Content(NodeUtils::toNode($this->converterManager->convert($content, $mime, [\stdClass::class])
								->convert()
								->getContent(), null, Element::class), IElement::class);
					}

				/** @noinspection PhpMissingBreakStatementInspection */
				case 'text/json':
					$target = 'text/plain';
				case 'stream+application/json':
				case 'application/json':
				case '*/*':
					$this->unsupported($content, $target, $content instanceof INode);
					return new Content($this->converterManager->convert($content, INode::class, ['application/json'])
						->convert()
						->getContent(), $target);
				case 'text/xml':
					return $this->converterManager->convert($content, INode::class, ['text/xml'])
						->convert();
			}
			return $this->exception($mime, $target);
		}
	}
