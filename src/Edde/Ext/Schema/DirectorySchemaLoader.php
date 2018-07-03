<?php
	declare(strict_types=1);

	namespace Edde\Ext\Schema;

	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\File\IDirectory;
	use Edde\Api\Node\INode;
	use Edde\Common\Schema\AbstractSchemaLoader;

	class DirectorySchemaLoader extends AbstractSchemaLoader {
		use LazyConverterManagerTrait;
		/**
		 * @var IDirectory
		 */
		protected $directory;
		/**
		 * @var string
		 */
		protected $mask;

		/**
		 * Q: What do computers and air conditions have in common?
		 * A: They're both become useless when you open windows.
		 *
		 * @param IDirectory $directory
		 * @param string     $mask
		 */
		public function __construct(IDirectory $directory, string $mask) {
			$this->directory = $directory;
			$this->mask = $mask;
		}

		/**
		 * @inheritdoc
		 */
		public function load() {
			foreach ($this->directory as $file) {
				if ($file->match($this->mask)) {
					yield $this->converterManager->convert($file, $file->getMime(), [INode::class])->convert()->getContent();
				}
			}
		}
	}
