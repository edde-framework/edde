<?php
	declare(strict_types=1);
	namespace Edde\Common\Response;

		use Edde\Api\Content\IContent;
		use Edde\Api\Response\IResponse;
		use Edde\Common\Object\Object;

		class Response extends Object implements IResponse {
			/**
			 * @var IContent
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
