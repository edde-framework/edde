<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhereGroup extends IFragment, \IteratorAggregate {
			/**
			 * where and relation
			 *
			 * @return IWhere
			 */
			public function and (): IWhere;

			/**
			 * where or relation
			 *
			 * @return IWhere
			 */
			public function or (): IWhere;

			/**
			 * @return ISchemaFragment
			 */
			public function getSchemaFragment(): ISchemaFragment;

			/**
			 * @return \Traversable|IWhere[]
			 */
			public function getIterator();
		}
