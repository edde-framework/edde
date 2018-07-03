<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeUtils;

	/**
	 * Specific converter for including php files which should return array (object).
	 */
	class PhpConverter extends AbstractConverter {
		/**
		 * You know you've been online too long when:
		 *
		 * Tech support calls YOU for help.
		 */
		public function __construct() {
			$this->register([
				'text/x-php',
				'application/x-php',
			], [
				INode::class,
				'array',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws NodeException
		 */
		public function convert($convert, string $source, string $target, string $mime) {
			$this->unsupported($convert, $target, $convert instanceof IResource);
			switch ($target) {
				case INode::class:
					/** @noinspection UnnecessaryParenthesesInspection */
					return (function (IResource $resource, string $source, string $mime) {
						NodeUtils::node($root = new Node(), $this->convert($resource, $source, 'array', $mime));
						return $root;
					})($convert, $source, $mime);
				case 'array':
					/** @noinspection UsingInclusionReturnValueInspection */
					if (is_array($include = require (string)$convert->getUrl()) === false) {
						throw new ConverterException(sprintf('Convertion to [%s] failed: php file [%s] has not returned array.', $target, (string)$convert->getUrl()));
					}
					return $include;
			}
			$this->exception($source, $target);
		}
	}
