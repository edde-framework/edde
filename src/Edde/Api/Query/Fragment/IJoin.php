<?php
	namespace Edde\Api\Query\Fragment;

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
