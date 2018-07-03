<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;

	/**
	 * Helper is used for attribute value filtering.
	 */
	interface IHelper {
		/**
		 * when null is returned, next helper should be executed
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 * @param mixed $value
		 * @param array ...$parameterList
		 *
		 * @return mixed|null
		 */
		public function helper(INode $macro, ICompiler $compiler, $value, ...$parameterList);
	}
