<?php
	namespace Edde\Api\Schema;

		interface IRelation {
			/**
			 * get relation schema
			 *
			 * @return ISchema
			 */
			public function getSchema(): ISchema;

			/**
			 * return source link; link should be connected to relation schema and to
			 * a property of relation schema
			 *
			 * @return ILink
			 */
			public function getSourceLink(): ILink;

			/**
			 * return target link; link should be connected to relation schema and to
			 * a property of relation schema
			 *
			 * @return ILink
			 */
			public function getTargetLink(): ILink;
		}
