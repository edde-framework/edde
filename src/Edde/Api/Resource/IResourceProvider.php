<?php
	declare(strict_types=1);

	namespace Edde\Api\Resource;

	use Edde\Api\Config\IConfigurable;

	interface IResourceProvider extends IConfigurable {
		/**
		 * is the given resource name available as a resrouce?
		 *
		 * @param string      $name
		 * @param string|null $namespace
		 * @param array       ...$parameters
		 *
		 * @return bool
		 */
		public function hasResource(string $name, string $namespace = null, ...$parameters): bool;

		/**
		 * request resource by the name
		 *
		 * @param string      $name
		 * @param string|null $namespace
		 * @param array       $parameters
		 *
		 * @return IResource
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource;
	}
