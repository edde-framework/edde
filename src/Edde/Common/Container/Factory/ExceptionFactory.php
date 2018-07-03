<?php
	declare(strict_types=1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Common\Container\AbstractFactory;

	class ExceptionFactory extends AbstractFactory {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $message;
		/**
		 * @var string
		 */
		protected $exception;

		/**
		 * A young guy from Nebraska moves to Florida and goes to a big "everything under one roof" department store looking for a job.
		 * The Manager says, "Do you have any sales experience?"
		 * The kid says, "Yeah. I was a salesman back in Omaha."
		 * Well, the boss liked the kid and gave him the job.
		 * "You start tomorrow." I'll come down after we close and see how you did."
		 * His first day on the job was rough, but he got through it.
		 * After the store was locked up, the boss came down.
		 * "How many customers bought something from you today?
		 * The kid says, "One".
		 * The boss says, "Just one? Our sales people average 20 to 30 customers a day. How much was the sale for?"
		 * The kid says, "$101,237.65 ".
		 * The boss says, "$101,237.65? What the heck did you sell?"
		 *
		 * The kid says, "First, I sold him a small fish hook. Then I sold him a medium fishhook. Then I sold him a larger fishhook. Then I sold him a new fishing rod. Then I asked him where he was going fishing and he said down the coast, so I told him he was going to need a boat, so we went down to the boat department and I sold him a twin engine Boston Whaler. Then he said he didn't think his Honda Civic would pull it, so I took him down to the automotive department and sold him that 4x4 Expedition."
		 *
		 * The boss said, "A guy came in here to buy a fish hook and you sold him a BOAT and a TRUCK?"
		 * The kid said, "No, the guy came in here to buy Tampons for his wife, and I said, 'Dude, your weekend's shot, you should go fishing.'"
		 * Vote: Joke has 87.26 % from
		 *
		 * @param string $name
		 * @param string $exception
		 * @param string $message
		 */
		public function __construct(string $name, string $exception, string $message = null) {
			$this->name = $name;
			$this->message = $message;
			$this->exception = $exception;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			return $this->name === $dependency;
		}

		/**
		 * @inheritdoc
		 */
		public function createDependency(IContainer $container, string $dependency = null): IDependency {
			$exception = $this->exception;
			throw new $exception($this->message);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContainer $container, array $parameterList, IDependency $dependency, string $name = null) {
			$exception = $this->exception;
			throw new $exception($this->message);
		}
	}
