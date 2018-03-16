<?php
	declare(strict_types=1);
	namespace Edde\Common\Response;

	use Edde\Api\Response\IResponse;
	use Edde\Content\IContent;
	use Edde\Object;

	class Response extends Object implements IResponse {
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
