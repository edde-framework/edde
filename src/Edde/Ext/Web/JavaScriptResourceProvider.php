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
	class JavaScriptResourceProvider extends AbstractResourceProvider {
		use LazyAssetDirectoryTrait;
		use LazyRootDirectoryTrait;
		/**
		 * @var IDirectory
		 */
		protected $javaScriptDirectory;

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource {
			if ($this->javaScriptDirectory === null) {
				throw new UnknownResourceException('Javascript directory has not been set up (or setup failed).');
			}
			list($control) = empty($parameters) ? [false] : $parameters;
			if (is_object($control)) {
				$file = $this->rootDirectory->directory('src/' . ($namespace ? $namespace . '/' : '') . implode('/', array_slice(explode('\\', get_class($control)), -2, 1)) . '/assets/js')->file($name);
				if ($file->isAvailable()) {
					return $file;
				}
			}
			$file = $this->javaScriptDirectory->file($name = $name . ($namespace ? '-' . StringUtils::lower(str_replace('/', '.', $namespace)) . '-' . $name : ''));
			if ($file->isAvailable()) {
				return $file;
			}
			throw new UnknownResourceException(sprintf('Requested unknown javascript [%s].', $name));
		}

		/**
		 * @inheritdoc
		 */
		protected function handleSetup() {
			parent::handleSetup();
			try {
				$javaScriptDirectory = $this->assetDirectory->directory('js');
				$javaScriptDirectory->realpath();
				$this->javaScriptDirectory = $javaScriptDirectory;
			} catch (RealPathException $exception) {
			}
		}
	}
