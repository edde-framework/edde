<?php
	declare(strict_types=1);

	namespace Edde\Common\Html;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Control\ControlException;
	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Html\IHtmlView;
	use Edde\Api\Template\CompilerException;
	use Edde\Api\Template\LazyTemplateManagerTrait;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\File\FileUtils;

	/**
	 * Template trait can be used by any html control; it gives simple way to load a template (or snippet) with some little magic around.
	 */
	trait TemplateTrait {
		use LazyContainerTrait;
		use LazyTemplateManagerTrait;
		use LazyRequestTrait;
		use CacheTrait;

		public function template(array $snippetList = null) {
			$reflectionClass = new \ReflectionClass($this);
			if (($template = $this->cache->load($cacheId = ('template-list/' . $this->request->getId() . $reflectionClass->getName()))) === null) {
				$parent = $reflectionClass;
				$fileList = [];
				while ($parent) {
					$directory = FileUtils::normalize(dirname($parent->getFileName()));
					$fileList[] = $directory . '/../template/layout.xml';
					$fileList[] = $directory . '/layout.xml';
					$fileList[] = $directory . '/template/layout.xml';
					if ($this->request->hasAction()) {
						$fileList[] = $directory . '/template/action-' . $this->request->getActionName() . '.xml';
					}
					if ($this->request->hasHandle()) {
						$fileList[] = $directory . '/template/handle-' . $this->request->getHandleName() . '.xml';
					}
					$parent = $parent->getParentClass();
				}
				$fileList = array_reverse($fileList, true);
				$importList = [];
				foreach ($fileList as $file) {
					if (file_exists($file)) {
						if (strpos($file, 'layout.xml') !== false) {
							$layout = FileUtils::realpath($file);
							continue;
						}
						$importList[] = FileUtils::realpath($file);
					}
				}
				/** @noinspection UnSafeIsSetOverArrayInspection */
				if (isset($layout) === false) {
					$layout = array_shift($importList);
				}
				/** @noinspection PhpUndefinedVariableInspection */
				$this->cache->save($cacheId, $template = [
					$layout,
					$importList,
				]);
			}
			/** @noinspection PhpUndefinedVariableInspection */
			$this->snippet($template[0], $snippetList, $template[1]);
			return $this;
		}

		public function snippet(string $file, array $snippetList = null, array $importList = []) {
			if (($this instanceof IHtmlControl) === false) {
				throw new HtmlException(sprintf('Cannot use template trait on [%s]; it can be used only on [%s].', get_class($this), IHtmlControl::class));
			}
			/** @var $control IHtmlView */
			/** @var $template IHtmlTemplate */
			$control = $this;
			try {
				$template = AbstractHtmlTemplate::template($this->templateManager->template($file, $importList), $this->container);
				foreach ($snippetList ?: [null] as $snippet) {
					$template->snippet($control, $snippet);
				}
			} catch (CompilerException $exception) {
				throw $exception;
			} catch (TemplateException $exception) {
				$message = 'Template has failed; ' . ($snippetList ? sprintf("source files:\n%s", implode(', ', $snippetList)) : 'there are no files in the snippet list. Action/handler template was probably not found.');
				throw new ControlException($message, 0, $exception);
			}
			return $this;
		}
	}
