<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Resource\IResourceProvider;

	/**
	 * Application context; basically defines application type (so under one source root could run
	 * more applications, e.g. backend, administration and frontend, ...).
	 */
	interface IContext extends IResourceProvider {
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
		public function getGuid(): string;

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
