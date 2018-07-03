<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Node\NodeUtils;

	class NodeConverter extends AbstractConverter {
		public function __construct() {
			$this->register(\stdClass::class, INode::class);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws NodeException
		 */
		public function convert($convert, string $source, string $target, string $mime) {
			$this->unsupported($convert, $target, $convert instanceof \stdClass);
			switch ($target) {
				case INode::class:
					return NodeUtils::convert($convert);
			}
			$this->exception($source, $target);
		}
	}
