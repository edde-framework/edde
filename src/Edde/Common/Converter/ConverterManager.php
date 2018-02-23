<?php
	declare(strict_types=1);
	namespace Edde\Common\Converter;

	use Edde\Api\Content\IContent;
	use Edde\Api\Converter\Exception\ConverterException;
	use Edde\Api\Converter\IConvertable;
	use Edde\Api\Converter\IConverter;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Object\Object;

	class ConverterManager extends Object implements IConverterManager {
		/**
		 * @var IConverter[]
		 */
		protected $converterList = [];

		/**
		 * @inheritdoc
		 */
		public function registerConverter(IConverter $converter): IConverterManager {
			foreach ($converter->getSourceList() as $source) {
				foreach ($converter->getTargetList() as $target) {
					$this->converterList[$source . '|' . $target] = $converter;
				}
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerConverterList(array $converterList): IConverterManager {
			foreach ($converterList as $converter) {
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
				if (isset($this->converterList[$id = ($mime . '|' . $target)])) {
					$unknown = false;
					try {
						return new Convertable($this->converterList[$id], $content, $target);
					} catch (\Exception $exception) {
					}
				}
			}
			if ($targetList === null || $mime === reset($targetList)) {
				return new Convertable(new PassConverter(), $content, $content->getType());
			}
			throw new ConverterException(sprintf('Cannot convert %ssource mime [%s] to any of [%s].', $unknown ? 'unknown/unsupported ' : '', $mime, implode(', ', $targetList)), 0, $exception);
		}

		/**
		 * @inheritdoc
		 */
		public function convert(IContent $content, array $targetList = null): IContent {
			return $this->resolve($content, $targetList)->convert();
		}
	}
