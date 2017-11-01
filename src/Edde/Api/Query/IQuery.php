<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Config\IConfigurable;

		interface IQuery extends IConfigurable {
			/**
			 * type of this query
			 *
			 * @return string
			 */
			public function getType(): string;

			/**
			 * parameter list of this query
			 *
			 * @return array
			 */
			public function getParameterList(): array;

			/**
			 * return friendly name for this query (when something fails, it could help find which query has failed)
			 *
			 * @return string
			 */
			public function getDescription(): ?string;
		}
