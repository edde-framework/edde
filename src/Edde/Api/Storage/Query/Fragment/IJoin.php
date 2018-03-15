<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage\Query\Fragment;

	use Edde\Api\Storage\Query\IFragment;

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

		/**
		 * @return null|string
		 */
		public function getRelation(): ?string;
	}
