<?php
	declare(strict_types = 1);

	namespace Edde\Api\Schema;

	/**
	 * Defines relation between properties; every property which is link must point to another
	 * property.
	 */
	interface ISchemaLink {
		/**
		 * link name
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * initial property
		 *
		 * @return ISchemaProperty
		 */
		public function getSource(): ISchemaProperty;

		/**
		 * target property
		 *
		 * @return ISchemaProperty
		 */
		public function getTarget(): ISchemaProperty;
	}
