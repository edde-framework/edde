<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

	/**
	 * A transaction is a shortcut to have entity queue bound together with a
	 * storage to simply execute set of operations over entities.
	 */
		interface ITransaction {
			/**
			 * shortcut to enqueue a new entity
			 *
			 * @param IEntity $entity
			 *
			 * @return ITransaction
			 */
			public function queue(IEntity $entity): ITransaction;

			/**
			 * @return IEntityQueue
			 */
			public function getEntityQueue(): IEntityQueue;

			/**
			 * is there something to do?
			 *
			 * @return bool
			 */
			public function isEmpty(): bool;

			/**
			 * execute the transaction basically means whole stuff is sent to storage
			 * under real storage transaction
			 *
			 * @return ITransaction
			 */
			public function execute(): ITransaction;
		}
