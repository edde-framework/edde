<?php
	declare(strict_types=1);
	namespace Edde\Common\Response;

		use Edde\Api\Content\IContent;
		use Edde\Api\Response\Exception\NotCallableException;
		use Edde\Api\Response\Exception\NotIterableException;
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
				if (is_callable($iterable = $this->content->getContent()) === false) {
					throw new NotCallableException(sprintf('Content type [%s] of response is not callable.', $this->content->getType()));
				} else if (is_iterable($iterable = $iterable()) === false) {
					throw new NotIterableException(sprintf('Content type [%s] of response is not iterable.', $this->content->getType()));
				}
				foreach ($iterable as $chunk) {
					echo $chunk;
				}
				return $this;
			}
		}
