<?php
	declare(strict_types=1);
	namespace Edde\Api\Utils\Inject;

	use Edde\Api\Utils\IHttpUtils;

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
