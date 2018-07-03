<?php
	declare(strict_types=1);

	namespace Edde\Api\Acl;

	interface IAcl {
		/**
		 * register resource for this update list
		 *
		 * @param bool           $grant
		 * @param string         $resource
		 * @param \DateTime      $from
		 * @param \DateTime|null $until
		 *
		 * @return IAcl
		 */
		public function register(bool $grant, string $resource = null, \DateTime $from = null, \DateTime $until = null): IAcl;

		/**
		 * check if the given right is granted for this update
		 *
		 * @param string         $resource
		 * @param \DateTime|null $dateTime
		 * @param bool           $default
		 *
		 * @return bool
		 */
		public function can(string $resource, \DateTime $dateTime = null, bool $default = null): bool;

		/**
		 * reset whole ACL so nothing would be basically allowed
		 *
		 * @return IAcl
		 */
		public function reset(): IAcl;
	}
