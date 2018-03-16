<?php
	declare(strict_types=1);
	namespace Edde\Service\Converter;

	use Edde\Common\Converter\PassConverter;
	use Edde\Content\IContent;
	use Edde\Converter\IConverterManager;
	use Edde\Object;

	class ConverterManager extends Object implements IConverterManager {
		/**
		 * @var \Edde\Converter\IConverter[]
		 */
		protected $converters = [];

		/**
		 * @inheritdoc
		 */
		public function registerConverter(\Edde\Converter\IConverter $converter): IConverterManager {
			foreach ($converter->getSources() as $source) {
				foreach ($converter->getTargets() as $target) {
					$this->converters[$source . '|' . $target] = $converter;
				}
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerConverters(array $converters): IConverterManager {
			foreach ($converters as $converter) {
				$this->registerConverter($converter);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function resolve(IContent $content, array $targetList = null): \Edde\Converter\IConvertable {
			$exception = null;
			$unknown = true;
			$mime = $content->getType();
			foreach ($targetList ?? [] as $target) {
				if ($mime === $target) {
					return new \Edde\Converter\Convertable(new PassConverter(), $content, $mime);
				}
				if (isset($this->converters[$id = ($mime . '|' . $target)])) {
					$unknown = false;
					try {
						return new \Edde\Converter\Convertable($this->converters[$id], $content, $target);
					} catch (\Exception $exception) {
					}
				}
			}
			if ($targetList === null || $mime === reset($targetList)) {
				return new \Edde\Converter\Convertable(new PassConverter(), $content, $content->getType());
			}
			throw new \Edde\Converter\ConverterException(sprintf('Cannot convert %ssource mime [%s] to any of [%s].', $unknown ? 'unknown/unsupported ' : '', $mime, implode(', ', $targetList)), 0, $exception);
		}

		/**
		 * @inheritdoc
		 */
		public function convert(IContent $content, array $targetList = null): IContent {
			return $this->resolve($content, $targetList)->convert();
		}
	}
