<?php
	declare(strict_types=1);
	namespace Edde\Converter;

	use Edde\Config\IConfigurable;
	use Edde\Content\IContent;

	/**
	 * Implementation of general conversion mechanism which is useful
	 * for example on content negotiation during http request - an user
	 * does not take care about incoming data, it's enough to say, gimme
	 * array, and the magic in converter do the job. If it's possible :).
	 */
	interface IConverterManager extends IConfigurable {
		/**
		 * register the given converter
		 *
		 * @param IConverter $converter
		 *
		 * @return IConverterManager
		 */
		public function registerConverter(IConverter $converter): IConverterManager;

		/**
		 * register list of converters
		 *
		 * @param IConverter[] $converters
		 *
		 * @return IConverterManager
		 */
		public function registerConverters(array $converters): IConverterManager;

		/**
		 * choose convertable for the given content and target list
		 *
		 * @param IContent $content
		 * @param array    $targets
		 *
		 * @return IConvertable
		 *
		 * @throws ConverterException
		 */
		public function resolve(IContent $content, array $targets = null): IConvertable;

		/**
		 * execute the conversion from source to target
		 *
		 * @param IContent   $content
		 * @param array|null $targets
		 *
		 * @return IContent
		 *
		 * @throws ConverterException
		 */
		public function convert(IContent $content, array $targets = null): IContent;
	}
