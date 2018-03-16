<?php
	declare(strict_types=1);
	namespace Edde\Storage\Query\Fragment;

	use Edde\Storage\Query\IFragment;

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
