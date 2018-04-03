<?php
	declare(strict_types=1);
	namespace Edde;

	use Edde\Config\IConfigurable;
	use Edde\Container\IAutowire;

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
	class Obj3ct implements IAutowire, IConfigurable {
		use Configurable;
		use Autowire;

		/**
		 * because PHP has some cool shit things like it cannot call
		 * magic parent, this method helps to standardize parent::__clone calls
		 * around the code
		 */
		public function __clone() {
			$this->tInit = false;
			$this->tSetup = false;
		}
	}
