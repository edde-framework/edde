<?php
	declare(strict_types=1);

	namespace Edde\Api\File;

	interface IDirectoryIterator extends \IteratorAggregate {
		/**
		 * add directory to iterator; only before use
		 *
		 * @param IDirectory $directory
		 *
		 * @return IDirectoryIterator
		 */
		public function addDirectory(IDirectory $directory): IDirectoryIterator;

		/**
		 * @return IFile[]
		 */
		public function getIterator();
	}
