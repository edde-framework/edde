<?php
	declare(strict_types=1);
	namespace Edde\Common\Response;

	use Edde\Content\IContent;
	use Edde\Object;
	use Edde\Response\IResponse;

	class Response extends Object implements \Edde\Response\IResponse {
		/**
		 * @var \Edde\Content\IContent
		 */
		protected $content;

		public function __construct(IContent $content) {
			$this->content = $content;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(): IResponse {
			foreach ($this->content as $chunk) {
				echo $chunk;
			}
			return $this;
		}
	}
