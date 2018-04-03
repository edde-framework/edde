<?php
	declare(strict_types=1);
	namespace Edde\Service\Http;

	use Edde\Http\IHttpUtils;

	trait HttpUtils {
		/** @var IHttpUtils */
		protected $httpUtils;

		/**
		 * @param IHttpUtils $httpUtils
		 */
		public function injectHttpUtils(IHttpUtils $httpUtils) {
			$this->httpUtils = $httpUtils;
		}
	}
