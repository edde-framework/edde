<?php
	declare(strict_types=1);

	namespace Edde\Common\Web;

	use Edde\Api\Asset\LazyAssetStorageTrait;
	use Edde\Api\Filter\IFilter;
	use Edde\Api\Session\LazyFingerprintTrait;
	use Edde\Api\Web\ICompiler;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Resource\ResourceList;

	/**
	 * Base class for all compilers (js/css).
	 */
	abstract class AbstractCompiler extends ResourceList implements ICompiler {
		use LazyAssetStorageTrait;
		use LazyFingerprintTrait;
		use CacheTrait;
		use ConfigurableTrait;
		/**
		 * filters applied during compilation (or after)
		 *
		 * @var IFilter[]
		 */
		protected $filterList = [];

		/**
		 * @inheritdoc
		 */
		public function registerFilter(IFilter $filter): ICompiler {
			$this->filterList[] = $filter;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getPathList(): array {
			$pathList = [];
			foreach ($this->resourceList as $resource) {
				$resource = $this->assetStorage->store($resource);
				$pathList[$url] = $url = (string)$resource->getRelativePath();
			}
			return $pathList;
		}

		/**
		 * filter input content with current set of filters
		 *
		 * @param string $content
		 *
		 * @return string
		 */
		protected function filter(string $content): string {
			foreach ($this->filterList as $filter) {
				$filter->setup();
				$content = $filter->filter($content);
			}
			return $content;
		}
	}
