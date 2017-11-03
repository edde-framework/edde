<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		use Edde\Api\Schema\IRelation;
		use Edde\Api\Schema\ISchema;

		interface ISchemaFragment extends IFragment {
			/**
			 * select this schema for data retrieval
			 *
			 * @return ISchemaFragment
			 */
			public function select(): ISchemaFragment;

			/**
			 * should be this fragment used as a data source?
			 *
			 * @return bool
			 */
			public function isSelected(): bool;

			/**
			 * get schema of this fragment
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

			/**
			 * get alias of this schema
			 *
			 * @return string
			 */
			public function getAlias(): string;

			/**
			 * is there a where fragment?
			 *
			 * @return bool
			 */
			public function hasWhere(): bool;

			/**
			 * get the where fragment for this query
			 *
			 * @return IWhereGroup
			 */
			public function where(): IWhereGroup;

			/**
			 * link the given relation to this schema fragment
			 *
			 * @param IRelation $relation
			 * @param string    $alias
			 *
			 * @return ILink
			 */
			public function link(IRelation $relation, string $alias): ILink;

			/**
			 * return list of related schema fragments
			 *
			 * @return ILink[]
			 */
			public function getLinkList(): array;
		}
