<?php
	declare(strict_types=1);

	namespace Edde\Common\Asset;

	use Edde\Api\Asset\IAssetStorage;
	use Edde\Api\Asset\LazyAssetDirectoryTrait;
	use Edde\Api\Asset\LazyStorageDirectoryTrait;
	use Edde\Api\File\DirectoryException;
	use Edde\Api\File\FileException;
	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyRootDirectoryTrait;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\ResourceException;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\File\File;
	use Edde\Common\File\FileUtils;

	/**
	 * Simple and uniform way how to handle file storing.
	 */
	class AssetStorage extends AbstractDeffered implements IAssetStorage {
		use LazyRootDirectoryTrait;
		use LazyAssetDirectoryTrait;
		use LazyStorageDirectoryTrait;

		/**
		 * @inheritdoc
		 * @throws ResourceException
		 * @throws FileException
		 */
		public function store(IResource $resource) {
			$this->use();
			$url = $resource->getUrl();
			$directory = $this->storageDirectory->directory(sha1(dirname($url->getPath())));
			try {
				$directory->create();
			} catch (DirectoryException $e) {
				throw new ResourceException(sprintf('Cannot create store folder [%s] for the resource [%s].', $directory, $url), 0, $e);
			}
			FileUtils::copy($url->getAbsoluteUrl(), $file = $directory->filename($url->getResourceName()));
			return new File($file, dirname($this->assetDirectory->getDirectory()));
		}

		/**
		 * @inheritdoc
		 */
		public function allocate(string $name): IFile {
			$this->use();
			$file = $this->assetDirectory->file($name);
			$file->setBase(dirname($this->assetDirectory->getDirectory()));
			return $file;
		}

		/**
		 * @inheritdoc
		 * @throws ResourceException
		 */
		protected function prepare() {
			$this->assetDirectory->create();
			$this->storageDirectory->create();
			$this->rootDirectory->use();
			$this->assetDirectory->use();
			$this->storageDirectory->use();
			if (strpos($this->assetDirectory->getDirectory(), $this->rootDirectory->getDirectory()) === false) {
				throw new ResourceException(sprintf('Asset path [%s] is not in the given root [%s].', $this->assetDirectory, $this->rootDirectory));
			}
			if (strpos($this->storageDirectory->getDirectory(), $this->assetDirectory->getDirectory()) === false) {
				throw new ResourceException(sprintf('Storage path [%s] is not in the given root [%s].', $this->storageDirectory, $this->rootDirectory));
			}
		}
	}
