<?php
	declare(strict_types=1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Common\Container\AbstractFactory;

	class ProxyFactory extends AbstractFactory {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $target;
		/**
		 * @var string
		 */
		protected $method;
		protected $parameterList;

		/**
		 * A bus full of Nuns falls of a cliff and they all die.
		 * They arrive at the gates of heaven and meet St. Peter. St. Peter says to them "Sisters, welcome to Heaven. In a moment I will let you all though the pearly gates, but before I may do that, I must ask each of you a single question. Please form a single-file line." And they do so.
		 * St. Peter turns to the first Nun in the line and asks her "Sister, have you ever touched a penis?"
		 * The Sister Responds "Well... there was this one time... that I kinda sorta... touched one with the tip of my pinky finger..."
		 * St. Peter says "Alright Sister, now dip the tip of your pinky finger in the Holy Water, and you may be admitted." and she did so.
		 * St. Peter now turns to the second nun and says "Sister, have you ever touched a penis?" "Well.... There was this one time... that I held one for a moment..."
		 * "Alright Sister, now just wash your hands in the Holy Water, and you may be admitted" and she does so.
		 * Now at this, there is a noise, a jostling in the line. It seems that one nun is trying to cut in front of another! St. Peter sees this and asks the Nun "Sister Susan, what is this? There is no rush!"
		 * Sister Susan responds "Well if I'm going to have to gargle this stuff, I'd rather do it before Sister Mary sticks her ass in it!"
		 *
		 * @param string $name
		 * @param string $target
		 * @param string $method
		 * @param array  $parameterList
		 */
		public function __construct(string $name, string $target, string $method, array $parameterList = []) {
			$this->name = $name;
			$this->target = $target;
			$this->method = $method;
			$this->parameterList = $parameterList;
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
			return $container->getFactory($this->target, $this->name)->createDependency($container, $this->target);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContainer $container, array $parameterList, IDependency $dependency, string $name = null) {
			$method = $this->method;
			$instance = $container->create($this->target, $parameterList, $this->name);
			if ($instance instanceof IConfigurable) {
				$instance->setup();
			}
			return $instance->{$method}(...$this->parameterList);
		}
	}
