<?php
	declare(strict_types=1);
	namespace Edde\Common\Response;

		use Edde\Api\Response\IResponse;
		use Edde\Api\Response\IResponseService;
		use Edde\Common\Object\Object;

		class ResponseService extends Object implements IResponseService {
			/**
			 * @inheritdoc
			 */
			public function execute(IResponse $response) : IResponse {
			}
		}
