<?php
	declare(strict_types = 1);

	namespace Edde\Api\Web;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\File\IFile;
	use Edde\Api\Filter\IFilter;
	use Edde\Api\Resource\IResourceList;

	/**
	 * Implementation for web based compilers.
	 */
	interface ICompiler extends IResourceList, IDeffered {
		/**
		 * register filter to this compiler
		 *
		 * @param IFilter $filter
		 *
		 * @return ICompiler
		 */
		public function registerFilter(IFilter $filter): ICompiler;

		/**
		 * set current namespace (optionally) to create different output result set (for example when variables are used)
		 *
		 * @param string $namespace
		 *
		 * @return ICompiler
		 */
		public function setNamespace(string $namespace): ICompiler;

		/**
		 * general resource list to resource conversion (compilation)
		 *
		 * @param IResourceList $resourceList
		 *
		 * @return IFile
		 */
		public function compile(IResourceList $resourceList = null): IFile;
	}
