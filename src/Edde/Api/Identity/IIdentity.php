<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	use Edde\Api\Acl\IAcl;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Deffered\IDeffered;

	/**
	 * Identity is (usualy) mutable interface holding current state of an identity (user, cron, ...) in an application.
	 */
	interface IIdentity extends IDeffered {
		/**
		 * identity can have optionaly additional data (for example user's row from database)
		 *
		 * @param ICrate $crate
		 *
		 * @return IIdentity
		 */
		public function setIdentity(ICrate $crate = null): IIdentity;

		/**
		 * has this identity additional data?
		 *
		 * @return bool
		 */
		public function hasIdentity(): bool;

		/**
		 * return optional identity data; if there is no data, exception should be thrown
		 *
		 * @return ICrate
		 */
		public function getIdentity(): ICrate;

		/**
		 * when neede only meta structure, this can be used
		 *
		 * @param array $metaList
		 *
		 * @return IIdentity
		 */
		public function setMetaList(array $metaList): IIdentity;

		/**
		 * return particular meta data from identity
		 *
		 * @param string $name
		 * @param mixed $default
		 *
		 * @return mixed
		 */
		public function getMeta(string $name, $default = null);

		/**
		 * return current metadata structure
		 *
		 * @return array
		 */
		public function getMetaList(): array;

		/**
		 * identity can be updated in realtime (for example after login)
		 *
		 * @param string $name
		 *
		 * @return IIdentity
		 */
		public function setName(string $name): IIdentity;

		/**
		 * return identity's name
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * change the authenticated flag of this identity; this should be done only by a IAuthenticator
		 *
		 * @param bool $authenticated
		 *
		 * @return IIdentity
		 */
		public function setAuthenticated(bool $authenticated): IIdentity;

		/**
		 * identity can exists even with user's data, but this says in general if the identity is confirmed (authenticated)
		 *
		 * @return bool === true, identity (user) has been logged in and session is valid
		 */
		public function isAuthenticated(): bool;

		/**
		 * until ACL is set, every action will be forbidden if not authenticated, allowed if authenticated
		 *
		 * @param IAcl $acl
		 *
		 * @return IIdentity
		 */
		public function setAcl(IAcl $acl): IIdentity;

		/**
		 * return current acl set
		 *
		 * @return IAcl
		 */
		public function getAcl(): IAcl;

		/**
		 * can this identity access the given resource?
		 *
		 * @param string $resource
		 * @param \DateTime|null $dateTime optionally datetime can be specified; null will NOT disable time checks
		 *
		 * @return bool
		 */
		public function can(string $resource, \DateTime $dateTime = null): bool;
	}
