<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\IMacroSet;
	use Edde\Common\Deffered\AbstractDeffered;

	class MacroSet extends AbstractDeffered implements IMacroSet {
		/**
		 * @var IMacro[]
		 */
		protected $macroList = [];

		/**
		 * @inheritdoc
		 */
		public function getMacroList(): array {
			$this->use();
			return $this->macroList;
		}

		public function setMacroList(array $macroList) {
			$this->macroList = [];
			foreach ($macroList as $macro) {
				$this->registerMacro($macro);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerMacro(IMacro $macro): IMacroSet {
			$this->macroList[$macro->getName()] = $macro;
			return $this;
		}
	}
