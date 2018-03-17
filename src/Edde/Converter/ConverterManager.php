<?php
	declare(strict_types=1);
	namespace Edde\Converter;

	use Edde\Content\IContent;
	use Edde\Object;
	use Exception;

	class ConverterManager extends Object implements IConverterManager {
		/** @var IConverter[] */
		protected $converters = [];

		/** @inheritdoc */
		public function registerConverter(IConverter $converter): IConverterManager {
			foreach ($converter->getSources() as $source) {
				foreach ($converter->getTargets() as $target) {
					$this->converters[$source . '|' . $target] = $converter;
				}
			}
			return $this;
		}

		/** @inheritdoc */
		public function registerConverters(array $converters): IConverterManager {
			foreach ($converters as $converter) {
				$this->registerConverter($converter);
			}
			return $this;
		}

		/** @inheritdoc */
		public function resolve(IContent $content, array $targets = null): IConvertable {
			$exception = null;
			$unknown = true;
			$mime = $content->getType();
			foreach ($targets ?? [] as $target) {
				if ($mime === $target) {
					return new Convertable(new PassConverter(), $content, $mime);
				}
				if (isset($this->converters[$id = ($mime . '|' . $target)])) {
					$unknown = false;
					try {
						return new Convertable($this->converters[$id], $content, $target);
					} catch (Exception $_) {
					}
				}
			}
			if ($targets === null || $mime === reset($targets)) {
				return new Convertable(new PassConverter(), $content, $content->getType());
			}
			throw new ConverterException(sprintf('Cannot convert %ssource mime [%s] to any of [%s].', $unknown ? 'unknown/unsupported ' : '', $mime, implode(', ', $targets)), 0, $exception);
		}

		/** @inheritdoc */
		public function convert(IContent $content, array $targets = null): IContent {
			return $this->resolve($content, $targets)->convert();
		}
	}
