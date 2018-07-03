<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\IContent;
	use Edde\Common\Object;

	class Content extends Object implements IContent {
		/**
		 * @var mixed
		 */
		protected $content;
		/**
		 * @var string
		 */
		protected $mime;

		/**
		 * 3 Database SQL walked into a NoSQL bar.
		 * A little while later they walked out because they couldn't find a table.
		 *
		 * @param mixed  $content
		 * @param string $mime
		 */
		public function __construct($content, string $mime) {
			$this->content = $content;
			$this->mime = $mime;
		}

		/**
		 * @inheritdoc
		 */
		public function getContent() {
			return $this->content;
		}

		/**
		 * @inheritdoc
		 */
		public function getMime(): string {
			return $this->mime;
		}
	}
