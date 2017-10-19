<?php
	namespace Edde\Common\Query;

		use Edde\Api\Query\IQuery;
		use Edde\Common\Object\Object;

		abstract class AbstractQuery extends Object implements IQuery {
			/**
			 * @inheritdoc
			 */
			public function getParameterList(): array {
				return [];
			}
		}
