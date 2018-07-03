<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IConverter;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * Default implementation of a convertion manager.
	 */
	class ConverterManager extends AbstractDeffered implements IConverterManager {
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
		public function convert($convert, string $source = null, string $target = null) {
			if (empty($source) || empty($target)) {
				return $convert;
			}
			if ($source === $target) {
				return $convert;
			}
			$this->use();
			if (isset($this->converterList[$mime = ($source . '|' . $target)]) === false) {
				throw new ConverterException(sprintf('Cannot convert unknown source mime [%s] to [%s].', $source, $target));
			}
			return $this->converterList[$mime]->convert($convert, $source, $target, $mime);
		}
	}
