<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IContext;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Common\Resource\AbstractResourceProvider;
	use Edde\Common\Resource\UnknownResourceException;

	abstract class AbstractContext extends AbstractResourceProvider implements IContext {
		use LazyResourceManagerTrait;

		/**
		 * @return string
		 */
		public function getId(): string {
			return static::class;
		}

		/**
		 * @inheritdoc
		 */
		public function getGuid(): string {
			return sha1($this->getId());
		}

		/**
		 * @inheritdoc
		 */
		public function cascade(string $delimiter, string $name = null): array {
			return [];
		}

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource {
			foreach (array_merge($this->cascade('/'), [null]) as $cascade) {
				if ($this->resourceManager->hasResource($name, $cascade, ...$parameters)) {
					return $this->resourceManager->getResource($name, $cascade, ...$parameters);
				}
			}
			throw new UnknownResourceException(sprintf('Requested unknown resource [%s].', $name));
		}
	}
