<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * Convert "redirect" source to an appropriate answer to http or json request.
	 */
	class RedirectConverter extends AbstractConverter {
		use LazyHttpResponseTrait;

		/**
		 * You know you're a geek when...
		 *
		 * Nobody ever invites you to their house unless their computer is malfunctioning.
		 */
		public function __construct() {
			$this->register('redirect', [
				'http+text/html',
				'http+application/json',
				'http+application/xml',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($convert, string $source, string $target, string $mime) {
			$this->unsupported($convert, $target, is_string($convert));
			switch ($target) {
				case 'http+text/html':
				case 'http+application/xml':
					$this->httpResponse->header('Location', $convert);
					$this->httpResponse->send();
					return $convert;
				case 'http+application/json':
					$this->httpResponse->send();
					echo $convert = json_encode(['redirect' => $convert]);
					return $convert;
			}
			$this->exception($source, $target);
		}
	}
