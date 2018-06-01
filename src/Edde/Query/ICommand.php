<?php
	declare(strict_types=1);
	namespace Edde\Query;

	/**
	 * Command is compile query on native command (native query).
	 */
	interface ICommand {
		/**
		 * @return string
		 */
		public function getQuery(): string;

		/**
		 * @return string
		 */
		public function __toString(): string;
	}
