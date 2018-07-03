<?php
	declare(strict_types=1);

	namespace Edde\Api\Database;

	/**
	 * A little extrem: this holds connection string information for database connection.
	 */
	interface IDsn {
		/**
		 * return connection string
		 *
		 * @return string
		 */
		public function getDsn(): string;

		/**
		 * an ability to set arbitrary (proprietary) options to dsn
		 *
		 * @param string $option
		 * @param mixed  $value
		 *
		 * @return IDsn
		 */
		public function setOption(string $option, $value): IDsn;

		/**
		 * retrieve an dsn option or default value
		 *
		 * @param string $option
		 * @param null   $default
		 *
		 * @return mixed
		 */
		public function getOption(string $option, $default = null);

		/**
		 * retrieve list of options
		 *
		 * @return array
		 */
		public function getOptionList(): array;
	}
