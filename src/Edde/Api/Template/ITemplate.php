<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\File\IFile;

	interface ITemplate {
		/**
		 * set all required parameters to the template
		 *
		 * @param string                 $name
		 * @param ITemplateContext|array $context
		 * @param string|null            $namespace
		 * @param array                  ...$parameterList
		 *
		 * @return ITemplate
		 */
		public function template(string $name, $context = null, string $namespace = null, ...$parameterList): ITemplate;

		/**
		 * compile template without "execution"
		 *
		 * @param string      $name
		 * @param string|null $namespace
		 * @param array       ...$parameterList
		 *
		 * @return IFile
		 */
		public function compile(string $name, string $namespace = null, ...$parameterList): IFile;

		/**
		 * execute the given template
		 *
		 * @param string             $name
		 * @param ITemplateContext[] $context
		 * @param string|null        $namespace
		 * @param array              ...$parameterList
		 *
		 * @return ITemplate
		 */
		public function snippet(string $name, array $context, string $namespace = null, ...$parameterList): ITemplate;

		/**
		 * execute template (this result would be rendered/echoed)
		 *
		 * @return ITemplate
		 */
		public function execute(): ITemplate;

		/**
		 * return template string
		 *
		 * @return string
		 */
		public function string(): string;

		/**
		 * support to string conversion
		 *
		 * @return string
		 */
		public function __toString(): string;
	}
