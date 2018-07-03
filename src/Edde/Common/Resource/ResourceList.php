<?php
	declare(strict_types=1);

	namespace Edde\Common\Resource;

	use ArrayIterator;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceList;
	use Edde\Common\File\File;
	use Edde\Common\Object\Object;

	class ResourceList extends Object implements IResourceList {
		/**
		 * @var IResource[]
		 */
		protected $resourceList = [];

		/**
		 * @inheritdoc
		 */
		public function addFile(string $file): IResourceList {
			$this->addResource(new File($file));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addResource(IResource $resource) {
			$this->resourceList[(string)$resource->getUrl()] = $resource;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getResourceName() {
			return sha1(implode('', array_keys($this->resourceList)));
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			return $this->getResourceList();
		}

		/**
		 * @inheritdoc
		 */
		public function getResourceList() {
			return new ArrayIterator($this->resourceList);
		}

		/**
		 * @inheritdoc
		 */
		public function getPathList(): array {
			$pathList = [];
			foreach ($this->resourceList as $resource) {
				$pathList[$url] = ($url = (string)$resource->getUrl());
			}
			return $pathList;
		}

		/**
		 * @inheritdoc
		 */
		public function isEmpty(): bool {
			return empty($this->resourceList);
		}

		/**
		 * @inheritdoc
		 */
		public function clear(): IResourceList {
			$this->resourceList = [];
			return $this;
		}
	}
