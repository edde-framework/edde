<?php
	declare(strict_types=1);

	namespace Edde\Api\Web;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\File\IFile;
	use Edde\Api\Filter\IFilter;
	use Edde\Api\Resource\IResourceList;

	/**
	 * Implementation for web based compilers.
	 */
	interface ICompiler extends IConfigurable, IResourceList {
		/**
		 * register filter to this compiler
		 *
		 * @param IFilter $filter
		 *
		 * @return ICompiler
		 */
		public function registerFilter(IFilter $filter): ICompiler;

		/**
		 * general resource list to resource conversion (compilation)
		 *
		 * @param IResourceList $resourceList
		 *
		 * @return IFile
		 */
		public function compile(IResourceList $resourceList = null): IFile;
	}
