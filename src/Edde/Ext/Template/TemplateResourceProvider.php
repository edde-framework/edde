<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\File\LazyRootDirectoryTrait;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Resource\AbstractResourceProvider;
	use Edde\Common\Resource\UnknownResourceException;

	class TemplateResourceProvider extends AbstractResourceProvider {
		use LazyRootDirectoryTrait;

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource {
			$file = $this->rootDirectory->directory('src/' . $namespace . '/templates')->file($name . '.xml');
			if ($file->isAvailable()) {
				return $file;
			}
			throw new UnknownResourceException(sprintf('Cannot get requested resource [%s]; no file matches.', $name));
		}
	}
