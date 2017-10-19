<?php
	namespace Edde\Api\Query;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Node\INode;

		interface IQuery extends IConfigurable {
			/**
			 * create an IQL query node
			 *
			 * @return INode
			 */
			public function getQuery(): INode;

			/**
			 * parameter list of this query
			 *
			 * @return array
			 */
			public function getParameterList(): array;
		}
