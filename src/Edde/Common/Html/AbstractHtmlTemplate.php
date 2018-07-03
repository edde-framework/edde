<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Template\LazyTemplateManagerTrait;
	use Edde\Api\Template\TemplateException;
	use Edde\Api\Translator\LazyTranslatorTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Api\Web\LazyJavaScriptCompilerTrait;
	use Edde\Api\Web\LazyJavaScriptListTrait;
	use Edde\Api\Web\LazyStyleSheetCompilerTrait;
	use Edde\Api\Web\LazyStyleSheetListTrait;
	use Edde\Common\Template\AbstractTemplate;

	/**
	 * Abstract helper class fro all html based templates; this should be used only by a template generator.
	 */
	abstract class AbstractHtmlTemplate extends AbstractTemplate implements IHtmlTemplate, ILazyInject {
		use LazyContainerTrait;
		use LazyTemplateManagerTrait;
		use LazyJavaScriptCompilerTrait;
		use LazyStyleSheetCompilerTrait;
		use LazyJavaScriptListTrait;
		use LazyStyleSheetListTrait;
		use LazyTranslatorTrait;
		/**
		 * @var IHtmlTemplate[]
		 */
		protected $embeddedList = [];

		/**
		 * @param IFile $file
		 * @param IContainer $container
		 *
		 * @return IHtmlTemplate
		 */
		static public function template(IFile $file, IContainer $container): IHtmlTemplate {
			/** @noinspection UnnecessaryParenthesesInspection */
			(function (IUrl $url) {
				require_once $url->getAbsoluteUrl();
			})($file->getUrl());
			$class = str_replace('.php', '', $file->getName());
			return $container->inject(new $class());
		}

		/**
		 * @inheritdoc
		 */
		public function embedd(IHtmlTemplate $htmlTemplate): IHtmlTemplate {
			foreach ($htmlTemplate->getBlockList() as $block) {
				$this->embeddedList[$block] = $htmlTemplate;
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws TemplateException
		 */
		public function block(IHtmlControl $htmlControl, string $name): IHtmlControl {
			if (isset($this->embeddedList[$name]) === false) {
				throw new TemplateException(sprintf('Requested unknown embedded block [%s] for control [%s; %s].', $name, get_class($htmlControl), $htmlControl->getNode()
					->getPath()));
			}
			return $this->embeddedList[$name]->snippet($htmlControl, $name);
		}
	}
