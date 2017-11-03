<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		use Edde\Api\Schema\IRelation;

		/**
		 * Special kind of schema fragment used for relations.
		 */
		interface ILink extends ISchemaFragment {
			/**
			 * return relation
			 *
			 * @return IRelation
			 */
			public function getRelation(): IRelation;

			/**
			 * attach source data for relation
			 *
			 * @param array $source
			 *
			 * @return ILink
			 */
			public function source(array $source): ILink;

			/**
			 * @return array
			 */
			public function getSource(): array;
		}
