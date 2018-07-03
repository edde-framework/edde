<?php
	declare(strict_types=1);

	namespace Edde\Common\Translator\Dictionary;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IContent;
	use Edde\Api\File\FileException;
	use Edde\Api\File\IFile;
	use Edde\Api\Translator\IDictionary;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Converter\Content;

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

		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws FileException
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			/** @var $content IFile */
			$this->unsupported($content, $target, $content instanceof IFile);
			switch ($target) {
				case IDictionary::class:
					/** @var $csvDictionary CsvDictionary */
					$csvDictionary = $this->container->create(CsvDictionary::class, [], __METHOD__);
					$csvDictionary->addFile($content->getPath());
					return new Content($csvDictionary, IDictionary::class);
			}
			return $this->exception($mime, $target);
		}
	}
