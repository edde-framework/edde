<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\File\FileException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\LazyHelperSetTrait;
	use Edde\Api\Template\LazyMacroSetTrait;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\File\File;

	/**
	 * Default implementation of a template manager.
	 */
	class TemplateManager extends AbstractDeffered implements ITemplateManager {
		use LazyContainerTrait;
		use LazyMacroSetTrait;
		use LazyHelperSetTrait;
		use CacheTrait;

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function template(string $template, array $importList = []) {
			$this->use();
			if ($result = $this->cache->load($cacheId = $template . implode(',', $importList))) {
				return $result;
			}
			/** @var $compiler ICompiler */
			$this->container->inject($compiler = new Compiler(new File($template)));
			foreach ($importList as &$import) {
				$import = new File($import);
			}
			unset($import);
			$compiler->registerMacroSet($this->macroSet);
			$compiler->registerHelperSet($this->helperSet);
			return $this->cache->save($cacheId, $compiler->template($importList));
		}

		protected function prepare() {
			parent::prepare();
			$this->cache();
		}
	}
