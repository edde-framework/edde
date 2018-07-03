<?php
	declare(strict_types = 1);

	use Edde\Common\Control\AbstractControl;

	class TestControl extends AbstractControl {
		public $thisIsArray;
		public $singleValue;

		public function someMethod($boo, $foo) {
			return $foo . $boo;
		}

		protected function action(string $action, array $parameterList) {
			if ($action === 'dummy') {
				return 'dumyyyy';
			}
			return parent::action($action, $parameterList);
		}
	}
