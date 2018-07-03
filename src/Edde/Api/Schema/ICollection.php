<?php
	declare(strict_types=1);

	namespace Edde\Api\Schema;

	interface ICollection {
		/**
		 * @return string
		 */
		public function getName(): string;

		/**
		 * get source property
		 *
		 * @return IProperty
		 */
		public function getSource(): IProperty;

		/**
		 * return target property
		 *
		 * @return IProperty
		 */
		public function getTarget(): IProperty;
	}
