<?php
	declare(strict_types=1);

	namespace Edde\Ext\Web;

	use Edde\Api\Asset\LazyAssetDirectoryTrait;
	use Edde\Api\File\IDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Common\File\RealPathException;
	use Edde\Common\Resource\AbstractResourceProvider;
	use Edde\Common\Resource\UnknownResourceException;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Standard resource provider based on IAssetDirectory.
	 */
	class ImageResourceProvider extends AbstractResourceProvider {
		use LazyAssetDirectoryTrait;
		/**
		 * @var IDirectory
		 */
		protected $imageDirectory;

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource {
			if ($this->imageDirectory === null) {
				throw new UnknownResourceException('Image directory has not been set up (or setup failed).');
			}
			$file = $this->imageDirectory->file($name = $name . ($namespace ? '-' . StringUtils::lower(str_replace('/', '.', $namespace)) . '-' . $name : ''));
			if ($file->isAvailable()) {
				return $file;
			}
			throw new UnknownResourceException(sprintf('Requested unknown image [%s].', $name));
		}

		/**
		 * @inheritdoc
		 */
		protected function handleSetup() {
			parent::handleSetup();
			try {
				$directory = $this->assetDirectory->directory('img');
				$directory->realpath();
				$this->imageDirectory = $directory;
			} catch (RealPathException $exception) {
			}
		}
	}
