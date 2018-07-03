<?php
	declare(strict_types = 1);

	namespace Edde\Common\Resource;

	use ArrayIterator;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceList;
	use Edde\Common\AbstractObject;
	use Edde\Common\File\File;

	class ResourceList extends AbstractObject implements IResourceList {
		/**
		 * @var IResource[]
		 */
		protected $resourceList = [];

		public function addFile(string $file): IResourceList {
			$this->addResource(new File($file));
			return $this;
		}

		public function addResource(IResource $resource) {
			$this->resourceList[(string)$resource->getUrl()] = $resource;
			return $this;
		}

		public function getResourceName() {
			return sha1(implode('', array_keys($this->resourceList)));
		}

		public function getIterator() {
			return $this->getResourceList();
		}

		public function getResourceList() {
			return new ArrayIterator($this->resourceList);
		}

		public function getPathList(): array {
			$pathList = [];
			foreach ($this->resourceList as $resource) {
				$pathList[$url] = ($url = (string)$resource->getUrl());
			}
			return $pathList;
		}

		public function isEmpty() {
			return empty($this->resourceList);
		}
	}
