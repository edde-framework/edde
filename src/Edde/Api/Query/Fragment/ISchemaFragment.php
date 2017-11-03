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
			 * add a relation to this fragment (schema) and return a related
			 * schema fragment (not $this)
			 *
			 * @param IRelation $relation
			 * @param array     $source data from where filtering data should be take (for example id, guid, ...)
			 * @param string    $alias  relation alias
			 *
			 * @return ISchemaFragment
			 */
			public function relation(IRelation $relation, array $source, string $alias): ISchemaFragment;
		}
