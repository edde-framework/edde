<?php
	declare(strict_types=1);

	namespace Edde\Common\Xml;

	use Edde\Api\File\IFile;
	use Edde\Api\Xml\IXmlExport;
	use Edde\Common\Object;

	abstract class AbstractXmlExport extends Object implements IXmlExport {
		/**
		 * @inheritdoc
		 */
		public function export(\Iterator $iterator, IFile $file): IFile {
			$file->openForWrite();
			$file->write($this->string($iterator));
			$file->close();
			return $file;
		}

		/**
		 * @inheritdoc
		 */
		public function string(\Iterator $iterator): string {
			ob_start();
			$this->node($iterator);
			return ob_get_clean();
		}
	}
