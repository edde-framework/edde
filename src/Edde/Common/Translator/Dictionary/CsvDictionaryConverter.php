<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator\Dictionary;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\ConverterException;
	use Edde\Api\File\FileException;
	use Edde\Api\File\IFile;
	use Edde\Api\Translator\IDictionary;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * Csv file support.
	 */
	class CsvDictionaryConverter extends AbstractConverter {
		use LazyContainerTrait;

		/**
		 * About 4,000 years ago:
		 *
		 * God: I shall create a great plague and every living thing on Earth will die!
		 *
		 * Fish: *Winks at God and slips him a $20 note*
		 *
		 * God: Correction, I shall create a great flood!
		 */
		public function __construct() {
			$this->register([
				'csv',
				'text/csv',
			], IDictionary::class);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws FileException
		 */
		public function convert($convert, string $source, string $target, string $mime) {
			/** @var $convert IFile */
			$this->unsupported($convert, $target, $convert instanceof IFile);
			switch ($target) {
				case IDictionary::class:
					$csvDictionary = $this->container->create(CsvDictionary::class);
					$csvDictionary->addFile($convert->getPath());
					return $csvDictionary;
			}
			$this->exception($source, $target);
		}
	}
