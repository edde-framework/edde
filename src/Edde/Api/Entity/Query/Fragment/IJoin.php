<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity\Query\Fragment;

		interface IJoin extends IFragment {
			/**
			 * @return string
			 */
			public function getSchema(): string;

			/**
			 * @return string
			 */
			public function getAlias(): string;

			/**
			 * @return bool
			 */
			public function isLink(): bool;
		}
