<?php
	declare(strict_types=1);
	namespace Edde\Api\Application;

	/**
	 * A context is feature which enables support for cascade factory, thus dynamically replace
	 * classes for example from Edde to the application.
	 *
	 * The context could be for example an user as an administrator, guest, and so on, which is
	 * quite cool, because only particular classes could be exchanged, thus code could be simpler
	 * and faster.
	 */
	interface IContext {
		/**
		 * return current id of context; could be any type of string
		 *
		 * @return string
		 */
		public function getId(): string;

		/**
		 * should return "hash" of id (could be arbitrary simple string)
		 *
		 * @return string
		 */
		public function getUuid(): string;

		/**
		 * return set of "base" namespaces where to search for the result
		 *
		 * @param string      $delimiter
		 * @param string|null $name
		 *
		 * @return array
		 */
		public function cascade(string $delimiter, string $name = null): array;
	}
