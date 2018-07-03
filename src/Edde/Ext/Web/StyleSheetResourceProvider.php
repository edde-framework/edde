<?php
	declare(strict_types=1);

	namespace Edde\Ext\Web;

	use Edde\Api\Asset\LazyAssetDirectoryTrait;
	use Edde\Api\File\IDirectory;
	use Edde\Api\File\LazyRootDirectoryTrait;
	use Edde\Api\Resource\IResource;
	use Edde\Common\File\RealPathException;
	use Edde\Common\Resource\AbstractResourceProvider;
	use Edde\Common\Resource\UnknownResourceException;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Standard resource provider based on IAssetDirectory.
	 */
	class StyleSheetResourceProvider extends AbstractResourceProvider {
		use LazyRootDirectoryTrait;
		use LazyAssetDirectoryTrait;
		/**
		 * @var IDirectory
		 */
		protected $styleSheetDirectory;

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource {
			if ($this->styleSheetDirectory === null) {
				throw new UnknownResourceException('Stylesheet directory has not been set up (or setup failed).');
			}
			list($control) = empty($parameters) ? [false] : $parameters;
			if (is_object($control)) {
				$file = $this->rootDirectory->directory('src/' . ($namespace ? $namespace . '/' : '') . implode('/', array_slice(explode('\\', get_class($control)), -2, 1)) . '/assets/css')->file($name);
				if ($file->isAvailable()) {
					return $file;
				}
			}
			$file = $this->styleSheetDirectory->file($name = $name . ($namespace ? '-' . StringUtils::lower(str_replace('/', '.', $namespace)) . '-' . $name : ''));
			if ($file->isAvailable()) {
				return $file;
			}
			throw new UnknownResourceException(sprintf('Requested unknown stylesheet [%s].', $name));
		}

		/**
		 * @inheritdoc
		 */
		protected function handleSetup() {
			parent::handleSetup();
			try {
				$styleSheetDirectory = $this->assetDirectory->directory('css');
				$styleSheetDirectory->realpath();
				$this->styleSheetDirectory = $styleSheetDirectory;
			} catch (RealPathException $exception) {
			}
		}
	}
