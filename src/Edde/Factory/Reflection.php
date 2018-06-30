<?php
	declare(strict_types=1);
	namespace Edde\Factory;

	use Edde\SimpleObject;

	class Reflection extends SimpleObject implements IReflection {
		/** @var IParameter[] */
		protected $injects;
		/** @var string[] */
		protected $configurators;

		/**
		 * A few days after Christmas, a mother was working in the kitchen listening to her young son playing with his new electric train in the living room.
		 * She heard the train stop and her son said, "All of you sons of b*tches who want off, get the hell off now, cause this is the last stop! And all of you sons of b*tches who are getting on, get your asses in the train, cause we're going down the tracks."
		 * The mother went nuts and told her son, "We don't use that kind of language in this house. Now I want you to go to your room and you are to stay there for TWO HOURS. When you come out, you may play with your train, but I want you to use nice language."
		 * Two hours later, the son comes out of the bedroom and resumes playing with his train. Soon the train stopped and the mother heard her son say, "All passengers who are disembarking from the train, please remember to take all of your belongings with you. We thank you for riding with us today and hope your trip was a pleasant one. We hope you will ride with us again soon."
		 * She hears the little boy continue, "For those of you just boarding, we ask you to stow all of your hand luggage under your seat. Remember, there is no smoking on the train. We hope you will have a pleasant and relaxing journey with us today."
		 * As the mother began to smile, the child added, "For those of you who are pissed off about the TWO HOUR delay, please see the b*tch in the kitchen."
		 *
		 * @param IParameter[] $injects
		 * @param string[]     $configurators
		 */
		public function __construct(array $injects = [], array $configurators = []) {
			$this->injects = $injects;
			$this->configurators = $configurators;
		}

		/** @inheritdoc */
		public function getInjects(): array {
			return $this->injects;
		}

		/** @inheritdoc */
		public function getConfigurators(): array {
			return $this->configurators;
		}
	}
