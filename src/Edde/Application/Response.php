<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Content\IContent;
	use Edde\Edde;

	class Response extends Edde implements IResponse {
		/** @var IContent */
		protected $content;
		/** @var int */
		protected $http;
		/** @var int */
		protected $exit;

		public function __construct(IContent $content) {
			$this->content = $content;
		}

		/** @inheritdoc */
		public function http(int $code): IResponse {
			$this->http = $code;
			return $this;
		}

		/** @inheritdoc */
		public function exit(int $code): IResponse {
			$this->exit = $code;
			return $this;
		}

		/** @inheritdoc */
		public function execute(): void {
			foreach ($this->content as $chunk) {
				echo $chunk;
			}
		}
	}
