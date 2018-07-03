<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Converter\Content;
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

		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws NodeException
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			$this->unsupported($content, $target, $content instanceof IResource);
			switch ($target) {
				case INode::class:
					return new Content((function (IResource $resource, string $source) {
						return NodeUtils::node(new Node(), $this->convert($resource, $source, 'array')->getContent());
					})($content, $mime), INode::class);
				case 'array':
					if (is_array($include = require (string)$content->getUrl()) === false) {
						throw new ConverterException(sprintf('Conversion to [%s] failed: php file [%s] has not returned array.', $target, (string)$content->getUrl()));
					}
					return new Content($include, 'array');
			}
			return $this->exception($mime, $target);
		}
	}
