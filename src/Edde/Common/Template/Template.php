<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Asset\LazyAssetStorageTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\Html\LazyHtmlGeneratorTrait;
	use Edde\Api\Resource\LazyResourceProviderTrait;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\LazyCompilerTrait;
	use Edde\Api\Template\LazyTemplateManagerTrait;
	use Edde\Api\Template\TemplateException;
	use Edde\Api\Web\LazyJavaScriptCompilerTrait;
	use Edde\Api\Web\LazyStyleSheetCompilerTrait;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Object;

	class Template extends Object implements ITemplate {
		use LazyResourceProviderTrait;
		use LazyContainerTrait;
		use LazyCompilerTrait;
		use LazyConverterManagerTrait;
		use LazyHtmlGeneratorTrait;
		use LazyAssetStorageTrait;
		use LazyStyleSheetCompilerTrait;
		use LazyJavaScriptCompilerTrait;
		use LazyTemplateManagerTrait;
		use CacheTrait;
		/**
		 * @var string
		 */
		protected $execute;

		/**
		 * @inheritdoc
		 */
		public function template(string $name, $context = null, string $namespace = null, ...$parameterList): ITemplate {
			$this->execute = [
				$name,
				$context ? (is_array($context) ? $context : [
					null       => $context,
					'.current' => $context,
				]) : [],
				$namespace,
				$parameterList,
			];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function compile(string $name, string $namespace = null, ...$parameterList): IFile {
			return $this->compiler->compile(($namespace ? $namespace . '-' : '') . $name, $this->resourceProvider->getResource($name, $namespace, ...$parameterList));
		}

		/**
		 * @inheritdoc
		 */
		public function snippet(string $name, array $context, string $namespace = null, ...$parameterList): ITemplate {
			$cache = $this->cache();
			/** @var $file IFile */
			if (($file = $cache->load($cacheId = ('template-' . $name . $namespace))) === null || $file->isAvailable() === false) {
				$cache->save($cacheId, $file = $this->compile($name, $namespace, ...$parameterList));
			}
			/** @noinspection PhpIncludeInspection */
			require $file;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(): ITemplate {
			if ($this->execute === null) {
				throw new TemplateException(sprintf('You have to prepare template by calling [%s::template()].', static::class));
			}
			$this->snippet($this->execute[0], $this->execute[1], $this->execute[2], ...$this->execute[3]);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function string(): string {
			ob_start();
			$this->execute();
			return ob_get_clean();
		}

		public function __clone() {
			parent::__clone();
			$this->execute = null;
		}

		/**
		 * @inheritdoc
		 */
		public function __toString(): string {
			return $this->string();
		}
	}
