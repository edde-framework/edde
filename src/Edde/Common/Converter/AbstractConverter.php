<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\IConverter;
	use Edde\Common\Object;

	/**
	 * Common stuff for converter implementation.
	 */
	abstract class AbstractConverter extends Object implements IConverter {
		/**
		 * @var string[]
		 */
		protected $mimeList;

		/**
		 * @param array|string      $source
		 * @param array|string|null $target
		 *
		 * @return $this
		 */
		public function register($source, $target = null) {
			$target = $target ?: '::call';
			$source = (array)$source;
			$target = (array)$target;
			/** @noinspection ForeachSourceInspection */
			foreach ($source as $src) {
				/** @noinspection ForeachSourceInspection */
				foreach ($target as $tgt) {
					$this->mimeList[$mime] = $mime = ($src . '|' . $tgt);
				}
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getMimeList(): array {
			return $this->mimeList;
		}

		/**
		 * @inheritdoc
		 */
		public function content(IContent $content, string $target = null): IContent {
			return $this->convert($content->getContent(), $content->getMime(), $target);
		}

		/**
		 * helper method to throw an exception if input is not supported
		 *
		 * @param mixed     $convert
		 * @param string    $target
		 * @param bool|null $check
		 *
		 * @throws ConverterException
		 */
		protected function unsupported($convert, string $target, bool $check = null) {
			if ($check) {
				return;
			}
			throw new ConverterException(sprintf('Cannot convert unsupported type [%s] to [%s] in [%s].', is_object($convert) ? get_class($convert) : gettype($convert), $target, static::class));
		}

		/**
		 * @param string $mime
		 * @param string $target
		 *
		 * @return IContent
		 * @throws ConverterException
		 */
		protected function exception(string $mime, string $target): IContent {
			throw new ConverterException(sprintf('Unsupported conversion in [%s] from [%s] to [%s].', static::class, $mime, $target));
		}
	}
