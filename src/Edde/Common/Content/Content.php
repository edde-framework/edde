<?php
	declare(strict_types=1);
	namespace Edde\Common\Content;

		use Edde\Api\Content\IContent;
		use Edde\Common\Object\Object;

		class Content extends Object implements IContent {
			/**
			 * @var mixed
			 */
			protected $content;
			/**
			 * @var string
			 */
			protected $type;

			/**
			 * @param mixed  $content
			 * @param string $type
			 */
			public function __construct($content, string $type) {
				$this->content = $content;
				$this->type = $type;
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
			public function getType(): string {
				return $this->type;
			}
		}
