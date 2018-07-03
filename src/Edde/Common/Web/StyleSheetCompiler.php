<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\File\FileException;
	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyTempDirectoryTrait;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Web\WebException;
	use Edde\Common\File\File;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Strings\StringException;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;

	/**
	 * Compiler for stylesheets.
	 */
	class StyleSheetCompiler extends AbstractCompiler implements IStyleSheetCompiler {
		use LazyTempDirectoryTrait;
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
			$this->use();
			$resourceList = $resourceList ?: $this;
			$content = [];
			$pathList = [];
			if (($file = $this->cache->load($cacheId = $resourceList->getResourceName())) === null) {
				foreach ($resourceList as $resource) {
					if ($resource->isAvailable() === false) {
						throw new WebException(sprintf('Cannot compile stylesheets: resource [%s] is not available (does not exists?).', (string)$resource->getUrl()));
					}
					$current = $this->filter($resource->get());
					$urlList = StringUtils::matchAll($current, "~url\\((?<url>['\"].*?['\"])\\)~", true);
					$resourcePath = $source = $resource->getUrl()
						->getPath();
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
							file_exists($file) === false && ($file = FileUtils::realpath($resourcePath . '/' . $path)) === false;
						} catch (FileException $exception) {
							throw new WebException(sprintf('Cannot locate css [%s] resource [%s] on the filesystem.', $source, $url), 0, $exception);
						}
						$current = str_replace($item, '"' . ($pathList[$path] = $this->assetStorage->store(new File($file))
								->getRelativePath()) . '"', $current);
					}
					$content[] = $current;
				}
				$this->cache->save($cacheId, $file = $this->assetStorage->store($this->tempDirectory->save($resourceList->getResourceName() . '.css', implode("\n", $content))));
			}
			return $file;
		}
	}
