<?php
	declare(strict_types=1);

	namespace Edde\Common\Web;

	use Edde\Api\File\FileException;
	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyTempDirectoryTrait;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Resource\LazyResourceProviderTrait;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Web\WebException;
	use Edde\Common\File\File;
	use Edde\Common\File\FileUtils;
	use Edde\Common\File\RealPathException;
	use Edde\Common\Resource\UnknownResourceException;
	use Edde\Common\Strings\StringException;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;

	/**
	 * Compiler for stylesheets.
	 */
	class StyleSheetCompiler extends AbstractCompiler implements IStyleSheetCompiler {
		use LazyTempDirectoryTrait;
		use LazyResourceProviderTrait;
		/**
		 * ignored url schemes
		 *
		 * @var array
		 */
		static private $schemeList = [
			'data',
		];

		/**
		 * @inheritdoc
		 * @throws WebException
		 * @throws StringException
		 * @throws FileException
		 */
		public function compile(IResourceList $resourceList = null): IFile {
			$content = [];
			$pathList = [];
			$resourceList = $resourceList ?: $this;
			$cache = $this->cache();
			$this->resourceProvider->setup();
			if (($file = $cache->load($cacheId = $resourceList->getResourceName())) === null) {
				foreach ($resourceList as $resource) {
					if ($resource->isAvailable() === false) {
						throw new WebException(sprintf('Cannot compile stylesheets: resource [%s] is not available (does not exists?).', (string)$resource->getUrl()));
					}
					$current = $this->filter($resource->get());
					$urlList = StringUtils::matchAll($current, "~url\\(['\"](?<url>.*?)['\"]\\)~", true);
					$resourcePath = $source = $resource->getUrl()->getPath();
					$resourcePath = dirname($resourcePath);
					foreach (empty($urlList) ? [] : array_unique($urlList['url']) as $item) {
						$url = Url::create($file = str_replace([
							'"',
							"'",
						], null, $item));
						if (in_array($url->getScheme(), self::$schemeList, true)) {
							continue;
						}
						if (isset($pathList[$path = $url->getPath()])) {
							$current = str_replace($item, '"' . $pathList[$path] . '"', $current);
							continue;
						}
						try {
							$file = $this->resourceProvider->getResource($file);
						} catch (UnknownResourceException $exception) {
							try {
								$file = new File(FileUtils::realpath($resourcePath . '/' . $path));
							} catch (RealPathException $exception) {
								throw new WebException(sprintf('Stylesheet [%s] requested resource [%s] which is not available.', $source, $file), 0, $exception);
							}
						}
						$assetFile = $this->assetStorage->store($file);
						$current = str_replace($item, ($pathList[$path] = $assetFile->getRelativePath()), $current);
					}
					$content[] = $current;
				}
				$cache->save($cacheId, $file = $this->assetStorage->store($this->tempDirectory->save($resourceList->getResourceName() . '.css', implode("\n", $content))));
			}
			return $file;
		}
	}
