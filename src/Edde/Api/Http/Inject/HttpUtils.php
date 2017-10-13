<?php
	declare(strict_types=1);
	namespace Edde\Api\Http\Inject;

		use Edde\Api\Http\IHttpUtils;

		trait HttpUtils {
			/**
			 * @var IHttpUtils
			 */
			protected $httpUtils;

			/**
			 * @param IHttpUtils $httpUtils
			 */
			public function lazyHttpUtils(IHttpUtils $httpUtils) {
				$this->httpUtils = $httpUtils;
			}
		}
