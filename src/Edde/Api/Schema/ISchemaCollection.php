<?php
	declare(strict_types = 1);

	namespace Edde\Api\Schema;

	interface ISchemaCollection {
		/**
		 * @return string
		 */
		public function getName();

		/**
		 * get source property
		 *
		 * @return ISchemaProperty
		 */
		public function getSource();

		/**
		 * return target property
		 *
		 * @return ISchemaProperty
		 */
		public function getTarget();
	}
