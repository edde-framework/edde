<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\Inject\ConverterManager;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Converter\Content;
	use Edde\Common\Node\NodeUtils;

	class NodeConverter extends AbstractConverter {
		use ConverterManager;

		public function __construct() {
			$this->register([
				'object',
				\stdClass::class,
			], INode::class);
			$this->register(INode::class, [
				'object',
				\stdClass::class,
				'application/json',
				'text/json',
			]);
		}

		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws NodeException
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			switch ($target) {
				case INode::class:
					$this->unsupported($content, $target, $content instanceof \stdClass);
					return new Content(NodeUtils::toNode($content), INode::class);
				case 'object':
				case \stdClass::class:
					$this->unsupported($content, $target, $content instanceof INode);
					return new Content(NodeUtils::fromNode($content), \stdClass::class);
				case 'application/json':
				case 'text/json':
					$this->unsupported($content, $target, $content instanceof INode);
					return $this->converterManager->convert(NodeUtils::fromNode($content), \stdClass::class, [$target])->convert();
			}
			return $this->exception($mime, $target);
		}
	}
