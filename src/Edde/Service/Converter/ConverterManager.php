<?php
	declare(strict_types=1);
	namespace Edde\Service\Converter;

	use Edde\Api\Converter\IConvertable;
	use Edde\Api\Converter\IConverter;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Converter\Convertable;
	use Edde\Common\Converter\PassConverter;
	use Edde\Content\IContent;
	use Edde\Object;

	class ConverterManager extends Object implements IConverterManager {
		/**
		 * @var IConverter[]
		 */
		protected $converters = [];

		/**
		 * @inheritdoc
		 */
		public function registerConverter(IConverter $converter): IConverterManager {
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
		public function resolve(IContent $content, array $targetList = null): IConvertable {
			$exception = null;
			$unknown = true;
			$mime = $content->getType();
			foreach ($targetList ?? [] as $target) {
				if ($mime === $target) {
					return new Convertable(new PassConverter(), $content, $mime);
				}
				if (isset($this->converters[$id = ($mime . '|' . $target)])) {
					$unknown = false;
					try {
						return new Convertable($this->converters[$id], $content, $target);
					} catch (\Exception $exception) {
					}
				}
			}
			if ($targetList === null || $mime === reset($targetList)) {
				return new Convertable(new PassConverter(), $content, $content->getType());
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
