<?php
	declare(strict_types=1);

	namespace Edde\Api\Converter;

	/**
	 * Support for a generic type conversion.
	 */
	interface IConverter {
		/**
		 * get list of supported mime types (or generic identifiers); they should be used only as alias (for example application/json, text/json, ...) and not for
		 * logical differentiating of types; in other words - all mime list must be compatible with all (internally supported) targets (not only combinations)
		 *
		 * @return array
		 */
		public function getMimeList(): array;

		/**
		 * convert input type to a output defined by a target; same target however can have more output types (for example string, node, ...)
		 *
		 * @param mixed  $content what to convert
		 * @param string $mime    source mime (should match "convert"
		 * @param string $target  target mime (general identifier); converter should throw an exception if a target is unknown/unsuporrted
		 *
		 * @return mixed
		 */
		public function convert($content, string $mime, string $target = null): IContent;

		/**
		 * @param IContent $content
		 * @param string   $target
		 *
		 * @return mixed
		 */
		public function content(IContent $content, string $target = null): IContent;
	}
