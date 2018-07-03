<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\IConvertable;
	use Edde\Api\Converter\IConverter;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	/**
	 * Default implementation of a conversion manager.
	 */
	class ConverterManager extends Object implements IConverterManager {
		use ConfigurableTrait;
		/**
		 * @var IConverter[]
		 */
		protected $converterList = [];

		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function registerConverter(IConverter $converter, bool $force = false): IConverterManager {
			foreach ($converter->getMimeList() as $mime) {
				if (isset($this->converterList[$mime]) && $force === false) {
					throw new ConverterException(sprintf('Converter [%s] has conflict with converter [%s] on mime [%s].', get_class($converter), get_class($this->converterList[$mime]), $mime));
				}
				$this->converterList[$mime] = $converter;
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($content, string $mime, array $targetList): IConvertable {
			return $this->content(new Content($content, $mime), $targetList);
		}

		/**
		 * @inheritdoc
		 */
		public function content(IContent $content, array $targetList = null): IConvertable {
			$exception = null;
			$unknown = true;
			$mime = $content->getMime();
			foreach ($targetList ?? [] as $target) {
				if ($mime === $target) {
					return new Convertable(new PassConverter(), $content, $content->getMime());
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
				return new Convertable(new PassConverter(), $content, $content->getMime());
			}
			throw new ConverterException(sprintf('Cannot convert %ssource mime [%s] to any of [%s].', $unknown ? 'unknown/unsupported ' : '', $mime, implode(', ', $targetList)), 0, $exception);
		}
	}
