<?php
	declare(strict_types=1);
	namespace Edde\Response;

	use Edde\Content\IContent;
	use Edde\Object;

	class Response extends Object implements IResponse {
		/** @var IContent */
		protected $content;

		public function __construct(IContent $content) {
			$this->content = $content;
		}

		/** @inheritdoc */
		public function execute(): IResponse {
			foreach ($this->content as $chunk) {
				echo $chunk;
			}
			return $this;
		}
	}
