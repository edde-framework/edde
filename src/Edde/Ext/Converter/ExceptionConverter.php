<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\IContent;
	use Edde\Common\Converter\AbstractConverter;

	class ExceptionConverter extends AbstractConverter {
		public function __construct() {
			$this->register([
				'exception',
			], [
				'http',
			]);
		}

		public function convert($content, string $mime, string $target = null): IContent {
			$this->unsupported($content, $target, $content instanceof \Exception);
			switch ($target) {
				case 'http':
					$headerList = $this->httpResponse->getHeaderList();
					if ($headerList->has('Content-Type') === false) {
						$this->httpResponse->header('Content-Type', 'text/plain');
					}
					$this->httpResponse->send();
					/**
					 * some shutdown handler should handle this
					 */
					throw $content;
			}
			return $this->exception($mime, $target);
		}
	}
