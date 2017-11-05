<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		interface ITransaction {
			/**
			 * add an entity to the transaction
			 *
			 * @param IEntity $entity
			 *
			 * @return ITransaction
			 */
			public function entity(IEntity $entity): ITransaction;

			/**
			 * return list of entities in this transaction; could be empty list
			 * if there are just relational (link) changes
			 *
			 * @return IEntity[]
			 */
			public function getEntityList(): array;

			/**
			 * make a link between the source and target entity
			 *
			 * @param IEntity $from
			 * @param IEntity $to
			 *
			 * @return ITransaction
			 */
			public function link(IEntity $from, IEntity $to): ITransaction;

			/**
			 * unlink the given entity from the source entity (remove a relation)
			 *
			 * @param IEntity $from
			 * @param IEntity $to
			 *
			 * @return ITransaction
			 */
			public function unlink(IEntity $from, IEntity $to): ITransaction;

			/**
			 * is there something to do?
			 *
			 * @return bool
			 */
			public function isEmpty(): bool;

			/**
			 * commit the transaction basically means whole stuff is sent to storage
			 * under real storage transaction
			 *
			 * @return ITransaction
			 */
			public function commit(): ITransaction;

			/**
			 * rollback just cleans everything in this transaction without affecting
			 * the real storage transaction
			 *
			 * @return ITransaction
			 */
			public function rollback(): ITransaction;
		}
