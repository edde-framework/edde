<?php
	declare(strict_types=1);

	namespace Edde\Common\Html\Converter;

	use Edde\Api\Control\ControlException;
	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Web\LazyJavaScriptCompilerTrait;
	use Edde\Api\Web\LazyJavaScriptListTrait;
	use Edde\Api\Web\LazyStyleSheetCompilerTrait;
	use Edde\Api\Web\LazyStyleSheetListTrait;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * IHtmlControl conversion to html output.
	 */
	class HtmlConverter extends AbstractConverter {
		use LazyHttpResponseTrait;
		use LazyJavaScriptCompilerTrait;
		use LazyStyleSheetCompilerTrait;
		use LazyJavaScriptListTrait;
		use LazyStyleSheetListTrait;

		/**
		 * HtmlConverter constructor.
		 */
		public function __construct() {
			$this->register(IHtmlControl::class, [
				'http+application/json',
				'application/json',
				'http+text/html',
				'text/html',
				'http+application/xml',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($convert, string $source, string $target, string $mime) {
			/** @var $convert IHtmlControl */
			if ($convert instanceof IHtmlControl === false) {
				$this->unsupported($convert, $target);
			}
			switch ($target) {
				/** @noinspection PhpMissingBreakStatementInspection */
				case 'http+application/json':
					$this->httpResponse->send();
				case 'application/json':
					$json = [];
					foreach ($convert->invalidate() as $control) {
						if (($id = $control->getId()) === '') {
							throw new ControlException(sprintf('Control [%s; %s] has no assigned id, thus it cannot be rendered.', get_class($control), $control->getNode()
								->getPath()));
						}
						$json['selector']['#' . $id] = [
							'action' => 'replace',
							'source' => $control->render(),
						];
					}
					if ($this->javaScriptCompiler->isEmpty() === false) {
						$json['javaScript'] = [
							$this->javaScriptCompiler->compile()
								->getRelativePath(),
						];
					}
					foreach ($this->javaScriptList as $resource) {
						$json['javaScript'][] = ((string)$resource->getUrl());
					}
					if ($this->styleSheetCompiler->isEmpty() === false) {
						$json['styleSheet'] = [
							$this->styleSheetCompiler->compile()
								->getRelativePath(),
						];
					}
					foreach ($this->styleSheetList as $resource) {
						$json['styleSheet'][] = ((string)$resource->getUrl());
					}
					echo $json = json_encode($json);
					return $json;
				/** @noinspection PhpMissingBreakStatementInspection */
				case 'http+text/html':
				case 'http+application/xml':
					$this->httpResponse->send();
				case 'text/html':
					echo $render = $convert->render();
					return $render;
			}
			$this->exception($source, $target);
		}
	}
