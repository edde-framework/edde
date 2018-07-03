<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\IConvertable;
	use Edde\Api\Converter\IConverter;
	use Edde\Common\Object\Object;

	class Convertable extends Object implements IConvertable {
		/**
		 * @var IConverter
		 */
		protected $converter;
		/**
		 * @var IContent
		 */
		protected $content;
		/**
		 * @var string
		 */
		protected $target;
		/**
		 * @var IContent
		 */
		protected $result;

		/**
		 * A biologist, a chemist and a statistician are out hunting.
		 * The biologist shoots at a deer and misses 5th to the left.
		 * The chemist takes a shot and misses 5th to the right.
		 * The statistician yells "We got 'em!"
		 *
		 * @param IConverter $converter
		 * @param mixed      $content
		 * @param string     $target
		 */
		public function __construct(IConverter $converter, IContent $content, string $target = null) {
			$this->converter = $converter;
			$this->content = $content;
			$this->target = $target;
		}

		/**
		 * @inheritdoc
		 */
		public function getContent(): IContent {
			return $this->content;
		}

		/**
		 * @inheritdoc
		 */
		public function getTarget() {
			return $this->target;
		}

		/**
		 * @inheritdoc
		 */
		public function convert(): IContent {
			if ($this->result === null) {
				$this->result = $this->converter->content($this->content, $this->target);
			}
			return $this->result;
		}
	}
