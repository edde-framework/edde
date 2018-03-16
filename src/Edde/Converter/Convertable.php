<?php
	declare(strict_types=1);
	namespace Edde\Converter;

	use Edde\Content\IContent;
	use Edde\Object;

	/**
	 * A biologist, a chemist and a statistician are out hunting.
	 * The biologist shoots at a deer and misses 5th to the left.
	 * The chemist takes a shot and misses 5th to the right.
	 * The statistician yells "We got 'em!"
	 */
	class Convertable extends Object implements IConvertable {
		/** @var IConverter */
		protected $converter;
		/** @var IContent */
		protected $content;
		/** @var string|null */
		protected $target;
		/** @var IContent */
		protected $result;

		/**
		 * @param IConverter $converter
		 * @param mixed      $content
		 * @param string     $target
		 */
		public function __construct(IConverter $converter, IContent $content, string $target = null) {
			$this->converter = $converter;
			$this->content = $content;
			$this->target = $target;
		}

		/** @inheritdoc */
		public function getConverter(): IConverter {
			return $this->converter;
		}

		/** @inheritdoc */
		public function getContent(): IContent {
			return $this->content;
		}

		/** @inheritdoc */
		public function getTarget() {
			return $this->target;
		}

		/** @inheritdoc */
		public function convert(): IContent {
			return $this->result ?: $this->result = $this->converter->convert($this->content, $this->target);
		}
	}
