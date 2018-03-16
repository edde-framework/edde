<?php
	declare(strict_types=1);
	namespace Edde\Xml;

	use Edde\Config\IConfigurable;
	use Edde\File\IFile;
	use Edde\Node\INode;
	use Iterator;

	interface IXmlExport extends IConfigurable {
		/**
		 * export the given node to the xml file
		 *
		 * @param Iterator|INode[] $iterator
		 * @param IFile            $file
		 *
		 * @return IFile
		 */
		public function export(Iterator $iterator, IFile $file): IFile;

		/**
		 * echoes the output of xml export
		 *
		 * @param Iterator|INode[] $iterator
		 *
		 * @return void
		 */
		public function node(Iterator $iterator): void;

		/**
		 * @param Iterator $iterator
		 *
		 * @return string
		 */
		public function string(Iterator $iterator): string;
	}
