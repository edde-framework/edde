<?php
	declare(strict_types=1);
	namespace Edde\Crypt;

	use Edde\Config\IConfigurable;

	/**
	 * Service for random byte generation.
	 */
	interface IRandomService extends IConfigurable {
		/**
		 * generate stream of bytes in given length
		 *
		 * @param int $length
		 *
		 * @return string
		 */
		public function bytes(int $length): string;

		/**
		 * generate random string with given character set (e.g. password)
		 *
		 * @param int    $length
		 * @param string $chars
		 *
		 * @return string
		 */
		public function generate(int $length = 10, string $chars = '0-9a-z'): string;

		/**
		 * generate standard uuid v4 (random)
		 *
		 * @param string|null $seed
		 *
		 * @return string
		 */
		public function uuid(string $seed = null): string;
	}
