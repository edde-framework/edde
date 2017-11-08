<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Schema\ILink;

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
			public function getEntities(): array;

			/**
			 * make a link between the source and target entity
			 *
			 * @param IEntity $from
			 * @param IEntity $to
			 * @param ILink   $link
			 *
			 * @return ITransaction
			 */
			public function link(IEntity $from, IEntity $to, ILink $link): ITransaction;

			/**
			 * @return IEntityLink[]
			 */
			public function getEntityLinks(): array;

			/**
			 * remove relation (1:n) to the given schema
			 *
			 * @param IEntity $entity
			 * @param ILink   $link
			 *
			 * @return ITransaction
			 */
			public function unlink(IEntity $entity, ILink $link): ITransaction;

			/**
			 * @return IEntityUnlink[]
			 */
			public function getEntityUnlinks(): array;

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

			/**
			 * commit all changes in this transaction back to it's entities
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
