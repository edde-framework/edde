<?php
	declare(strict_types=1);
	namespace Edde\Factory;

	interface IParameter {
		/**
		 * return name of a parameter
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * return class name of the parameter
		 *
		 * @return string
		 */
		public function getClass(): string;
	}
