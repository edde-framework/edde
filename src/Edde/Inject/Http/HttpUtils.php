<?php
	declare(strict_types=1);
	namespace Edde\Inject\Http;

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
