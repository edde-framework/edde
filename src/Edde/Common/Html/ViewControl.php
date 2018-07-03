<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Html\IHtmlView;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Link\LazyLinkFactoryTrait;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceList;
	use Edde\Common\File\File;
	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Common\Html\Document\MetaControl;

	/**
	 * Formal root control for displaying page with some shorthands.
	 */
	class ViewControl extends DocumentControl implements IHtmlView {
		use LazyHttpRequestTrait;
		use LazyLinkFactoryTrait;
		use LazyResponseManagerTrait;
		use TemplateTrait;
		use ResponseTrait;
		use RedirectTrait;

		/**
		 * @inheritdoc
		 */
		public function setAttribute($attribute, $value) {
			/** @noinspection DegradedSwitchInspection */
			switch ($attribute) {
				case 'title':
					$this->setTitle($value);
					break;
				default:
					parent::setAttribute($attribute, $value);
			}
			return $this;
		}

		public function setTitle($title) {
			$this->getHead()
				->setTitle($title);
			return $this;
		}

		public function addStyleSheet(string $file) {
			$this->use();
			$this->styleSheetCompiler->addResource(new File($file));
			return $this;
		}

		public function addStyleSheetResource(IResource $resource) {
			$this->use();
			$this->styleSheetCompiler->addResource($resource);
			return $this;
		}

		public function addJavaScript(string $file) {
			$this->use();
			$this->javaScriptCompiler->addResource(new File($file));
			return $this;
		}

		public function addJavaScriptResource(IResource $resource) {
			$this->use();
			$this->javaScriptCompiler->addResource($resource);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function render(int $indent = 0): string {
			$this->use();
			if ($this->styleSheetCompiler->isEmpty() === false) {
				$this->head->addStyleSheet($this->styleSheetCompiler->compile()
					->getRelativePath());
			}

			foreach ($this->styleSheetList as $resource) {
				$this->head->addStyleSheet((string)$resource->getUrl());
			}

			if ($this->javaScriptCompiler->isEmpty() === false) {
				$this->head->addJavaScript($this->javaScriptCompiler->compile()
					->getRelativePath());
			}

			foreach ($this->javaScriptList as $resource) {
				$this->head->addJavaScript((string)$resource->getUrl());
			}

			$this->dirty();
			return parent::render($indent);
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			parent::prepare();
			$this->cache();
			$this->head->addControl($this->createControl(MetaControl::class)
				->setAttributeList([
					'name' => 'viewport',
					'content' => 'width=device-width, initial-scale=1',
				]));
		}
	}
