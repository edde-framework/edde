<?php
	declare(strict_types=1);
	namespace Edde\Common\Response;

		use Edde\Api\Response\Exception\NotIterableException;
		use Edde\Api\Response\IResponse;
		use Edde\Api\Response\IResponseService;
		use Edde\Common\Object\Object;

		class ResponseService extends Object implements IResponseService {
			/**
			 * @inheritdoc
			 */
			public function execute(IResponse $response): IResponseService {
				if (is_iterable($iterable = ($content = $response->getContent())->getContent()) === false) {
					throw new NotIterableException(sprintf('Content type [%s] of response is not iterable.', $content->getType()));
				}
				foreach ($iterable as $chunk) {
					echo $chunk;
				}
				return $this;
			}
		}
