<?php
	namespace Edde\Api\Schema;

		use Edde\Api\Config\IConfigurable;

		interface ISchema extends IConfigurable {
			/**
			 * return name of a schema (it could have even "namespace" like name)
			 *
			 * @return string
			 */
			public function getName(): string;
		}
