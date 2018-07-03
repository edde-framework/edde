<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crypt;

	/**
	 * Interface for encapsulating encryption, decryption and other related stuff.
	 */
	interface ICryptEngine {
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
		 * @param int $length
		 * @param string $charlist
		 *
		 * @return string
		 */
		public function generate(int $length = 10, string $charlist = '0-9a-z'): string;

		/**
		 * generate standard GUID
		 *
		 * @param string|null $seed
		 *
		 * @return string
		 */
		public function guid(string $seed = null): string;

		/**
		 * generate secure password one-way hash
		 *
		 * @param string $password
		 *
		 * @return string
		 */
		public function password(string $password): string;

		/**
		 * verify input string against hashed password
		 *
		 * @param string $source
		 * @param string $hash
		 *
		 * @return bool
		 */
		public function verify(string $source, string $hash): bool;

		/**
		 * ideally constant time implementation of base64 encode
		 *
		 * @param string $source
		 *
		 * @return string
		 */
		public function base64encode(string $source): string;

		/**
		 * ideally constant time implementation of base64 decode
		 *
		 * @param string $source
		 *
		 * @return string
		 */
		public function base64decode(string $source): string;
	}
