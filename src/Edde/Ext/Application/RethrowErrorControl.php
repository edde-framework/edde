<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Application;

	use Edde\Api\Application\IErrorControl;
	use Edde\Common\Control\AbstractControl;

	class RethrowErrorControl extends AbstractControl implements IErrorControl {
		public function exception(\Exception $e) {
			throw $e;
		}
	}
