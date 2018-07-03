<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * Basic http converter; it will convert http+text/plain and http+callback to output.
	 */
	class HttpConverter extends AbstractConverter {
		use LazyHttpResponseTrait;

		/**
		 * It is so cold outside I saw a politician with his hands in his own pockets.
		 */
		public function __construct() {
			$this->register([
				'text/plain',
				'string',
				'callback',
			], [
				'http+text/plain',
				'text/plain',
				'string',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($convert, string $source, string $target, string $mime) {
			if (is_callable($convert) === false && is_string($convert) === false) {
				$this->unsupported($convert, $target);
			}
			switch ($target) {
				/** @noinspection PhpMissingBreakStatementInspection */
				case 'http+text/plain':
					$this->httpResponse->send();
				case 'text/plain':
					if (is_callable($convert)) {
						$convert();
						return null;
					}
					echo $convert;
					return null;
				case 'string':
					if (is_callable($convert)) {
						return $convert();
					}
					return $convert;
			}
			$this->exception($source, $target);
		}
	}
