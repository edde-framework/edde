<?php
	namespace Edde\Api\Utils\Inject;

		use Edde\Api\Utils\ICliUtils;

		trait CliUtils {
			/**
			 * @var ICliUtils
			 */
			protected $cliUtils;

			/**
			 * @param ICliUtils $cliUtils
			 */
			public function lazyCliUtils(ICliUtils $cliUtils) {
				$this->cliUtils = $cliUtils;
			}
		}
