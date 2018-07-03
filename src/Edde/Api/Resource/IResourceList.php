<?php
	declare(strict_types=1);

	namespace Edde\Api\Resource;

	use Iterator;
	use IteratorAggregate;

	interface IResourceList extends IteratorAggregate {
		/**
		 * add resource to the resource list
		 *
		 * @param IResource $resource
		 *
		 * @return $this
		 */
		public function addResource(IResource $resource);

		/**
		 * @param string $file
		 *
		 * @return IResourceList
		 */
		public function addFile(string $file): IResourceList;

		/**
		 * return hash (name) based on the resources (for example based on a urls)
		 *
		 * @return string
		 */
		public function getResourceName();

		/**
		 * return iterator over this resource list
		 *
		 * @return IResource[]|Iterator
		 */
		public function getResourceList();

		/**
		 * @return array
		 */
		public function getPathList(): array;

		/**
		 * @return bool
		 */
		public function isEmpty(): bool;

		/**
		 * clear resource list
		 *
		 * @return IResourceList
		 */
		public function clear(): IResourceList;

		/**
		 * @return IResource[]
		 */
		public function getIterator();
	}
