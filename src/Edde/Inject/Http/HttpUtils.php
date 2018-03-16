<?php
	declare(strict_types=1);
	namespace Edde\Inject\Http;

	trait HttpUtils {
		/**
		 * @var \Edde\Http\IHttpUtils
		 */
		protected $httpUtils;

		/**
		 * @param \Edde\Http\IHttpUtils $httpUtils
		 */
		public function lazyHttpUtils(\Edde\Http\IHttpUtils $httpUtils) {
			$this->httpUtils = $httpUtils;
		}
	}
