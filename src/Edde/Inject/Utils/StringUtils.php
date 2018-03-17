<?php
	declare(strict_types=1);
	namespace Edde\Inject\Utils;

	use Edde\Utils\IStringUtils;

	trait StringUtils {
		/**
		 * @var IStringUtils
		 */
		protected $stringUtils;

		/**
		 * @param IStringUtils $stringUtils
		 */
		public function lazyStringUtils(IStringUtils $stringUtils) {
			$this->stringUtils = $stringUtils;
		}
	}
