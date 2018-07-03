<?php
	declare(strict_types=1);

	namespace Edde\Api\Acl;

	interface IAclManager {
		/**
		 * general method for enable/disable access for the given group; order of access/grant/deny is important
		 *
		 * @param string         $group
		 * @param bool           $grant
		 * @param string         $resource
		 * @param \DateTime      $from
		 * @param \DateTime|null $until
		 *
		 * @return IAclManager
		 */
		public function access(string $group, bool $grant, string $resource = null, \DateTime $from = null, \DateTime $until = null): IAclManager;

		/**
		 * grant right to the given group; order of access/grant/deny is important
		 *
		 * @param string         $group
		 * @param string         $resource if null, "root" access is granted
		 * @param \DateTime      $from
		 * @param \DateTime|null $until
		 *
		 * @return IAclManager
		 */
		public function grant(string $group, string $resource = null, \DateTime $from = null, \DateTime $until = null): IAclManager;

		/**
		 * deny right to the given group; order of access/grant/deny is important
		 *
		 * @param string         $group
		 * @param string         $resource if null, everything is denied (can be changed by ongoing rules)
		 * @param \DateTime      $from
		 * @param \DateTime|null $until
		 *
		 * @return IAclManager
		 */
		public function deny(string $group, string $resource = null, \DateTime $from = null, \DateTime $until = null): IAclManager;

		/**
		 * build effective ACL from the given list of groups
		 *
		 * @param IAcl  $acl
		 * @param array $groupList
		 *
		 * @return IAclManager
		 */
		public function update(IAcl $acl, array $groupList): IAclManager;
	}
