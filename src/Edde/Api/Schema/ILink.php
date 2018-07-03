<?php
	declare(strict_types=1);

	namespace Edde\Api\Schema;

	/**
	 * Defines relation between properties; every property which is link must point to another
	 * property.
	 */
	interface ILink {
		/**
		 * link name
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * initial property
		 *
		 * @return IProperty
		 */
		public function getSource(): IProperty;

		/**
		 * target property
		 *
		 * @return IProperty
		 */
		public function getTarget(): IProperty;
	}
