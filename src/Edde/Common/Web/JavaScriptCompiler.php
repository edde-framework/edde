<?php
	declare(strict_types=1);

	namespace Edde\Common\Web;

	use Edde\Api\File\IFile;
	use Edde\Api\File\LazyTempDirectoryTrait;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Web\IJavaScriptCompiler;

	/**
	 * JavaScript "minifier" resource compiler.
	 */
	class JavaScriptCompiler extends AbstractCompiler implements IJavaScriptCompiler {
		use LazyTempDirectoryTrait;

		/**
		 * @inheritdoc
		 */
		public function compile(IResourceList $resourceList = null): IFile {
			$content = [];
			$resourceList = $resourceList ?: $this;
			$cache = $this->cache();
			if (($file = $cache->load($cacheId = $resourceList->getResourceName())) === null) {
				foreach ($resourceList as $resource) {
					$content[] = $this->filter($resource->get());
				}
				$cache->save($cacheId, $file = $this->assetStorage->store($this->tempDirectory->save($resourceList->getResourceName() . '.js', implode(";\n", $content))));
			}
			return $file;
		}
	}
