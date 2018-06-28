<?php
	declare(strict_types=1);
	namespace Edde\Container;

	use Edde\Factory\IParameter;

	/**
	 * This is a formal interface for classes supporting autowiring. The specification
	 * forces container to respect this interface, thus inject all dependencies based on
	 * all available inject methods.
	 */
	interface IAutowire {
		/**
		 * @param IParameter[] $parameters
		 * @param IContainer   $container
		 *
		 * @return mixed
		 */
		public function autowires(array $parameters, IContainer $container);
	}
