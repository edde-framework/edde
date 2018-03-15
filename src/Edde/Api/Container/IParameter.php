<?php
	declare(strict_types=1);
	namespace Edde\Api\Container;

	interface IParameter {
		/**
		 * return name of a parameter
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * is this parameter optional?
		 *
		 * @return bool
		 */
		public function isOptional(): bool;

		/**
		 * return class name of the parameter
		 *
		 * @return string
		 */
		public function getClass(): string;
	}
