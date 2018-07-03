<?php
	declare(strict_types=1);

	namespace Edde\Ext\Control;

	use Edde\Api\Control\IControl;
	use Edde\Api\File\LazyRootDirectoryTrait;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Resource\AbstractResourceProvider;
	use Edde\Common\Resource\UnknownResourceException;

	class ControlTemplateResourceProvider extends AbstractResourceProvider {
		use LazyRootDirectoryTrait;

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource {
			if (count($parameters) !== 1) {
				throw new UnknownResourceException(sprintf('Cannot get requested resource [%s]; missing control instance parameter.', $name));
			}
			/** @var $control IControl */
			list($control) = $parameters;
			$file = $this->rootDirectory->directory('src/' . ($namespace ? $namespace . '/' : '') . implode('/', array_slice(explode('\\', get_class($control)), -2, 1)) . '/templates')->file($name . '.xml');
			if ($file->isAvailable()) {
				return $file;
			}
			throw new UnknownResourceException(sprintf('Cannot get requested resource [%s]; no file matches.', $name));
		}
	}
