<?php
	declare(strict_types=1);

	namespace Edde\Common;

	use Edde\Api\Container\ILazyInject;
	use Edde\Common\Container\LazyTrait;

	/**
	 * While watching TV with his wife, a man tosses peanuts into the air and catches them in his mouth.
	 * Just as he throws another peanut into the air, the front door opens, causing him to turn his head.
	 * The peanut falls into his ear and gets stuck.
	 * His daughter comes in with her date.
	 * The man explains the situation, and the daughter's date says, "I can get the peanut out."
	 * He tells the father to sit down, shoves two fingers into the father's nose, and tells him to blow hard.
	 * The father blows, and the peanut flies out of his ear.
	 * After the daughter takes her date to the kitchen for something to eat, the mother turns to the father and says, "Isn't he smart? I wonder what he plans to be."
	 * The father says, "From the smell of his fingers, I'd say our son-in-law."
	 */
	class Object implements ILazyInject {
		use LazyTrait;
		protected $aId;

		/**
		 * return object hash (unique id); object has is NOT based on internal state
		 *
		 * @return string
		 */
		public function hash(): string {
			if ($this->aId === null) {
				$this->aId = hash('sha512', spl_object_hash($this));
			}
			return $this->aId;
		}

		protected function handleInit() {
		}

		protected function handleWarmup() {
		}

		protected function handleConfig() {
		}

		protected function handleSetup() {
		}

		public function __clone() {
			$this->aId = null;
		}
	}
