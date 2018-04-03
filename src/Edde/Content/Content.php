<?php
	declare(strict_types=1);
	namespace Edde\Content;

	use Edde\Obj3ct;

	class Content extends Obj3ct implements IContent {
		/** @var mixed */
		protected $content;
		/** @var string */
		protected $type;

		/**
		 * @param mixed  $content
		 * @param string $type
		 */
		public function __construct($content, string $type) {
			$this->content = $content;
			$this->type = $type;
		}

		/** @inheritdoc */
		public function getContent() {
			return $this->content;
		}

		/** @inheritdoc */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * simplest form of content throws just content outside
		 */
		public function getIterator() {
			yield $this->content;
		}
	}
