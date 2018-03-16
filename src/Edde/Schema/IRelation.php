<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	interface IRelation {
		/**
		 * return relation schema
		 *
		 * @return ISchema
		 */
		public function getSchema(): ISchema;

		/**
		 * @return ILink
		 */
		public function getFrom(): ILink;

		/**
		 * @return ILink
		 */
		public function getTo(): ILink;
	}
