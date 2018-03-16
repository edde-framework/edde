<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	/**
	 * Record is a row got from storage containing multpile entities
	 * separated by an alias (kind of result set).
	 */
	interface IRecord {
		/**
		 * get source for the given alias (this will not create an entity)
		 *
		 * @param string $alias
		 *
		 * @return array
		 *
		 * @throws EntityException
		 */
		public function getSource(string $alias): array;

		/**
		 * get an entity by the given alias
		 *
		 * @param string $alias
		 *
		 * @return IEntity
		 *
		 * @throws EntityException
		 */
		public function getEntity(string $alias): IEntity;
	}
