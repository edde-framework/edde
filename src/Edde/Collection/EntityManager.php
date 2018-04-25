<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Edde;
	use Edde\Schema\ISchema;
	use Edde\Service\Container\Container;
	use stdClass;

	class EntityManager extends Edde implements IEntityManager {
		use Container;

		/** @inheritdoc */
		public function entity(ISchema $schema, stdClass $default = null): IEntity {
			/** @var $entity IEntity */
			$entity = $this->container->create(Entity::class, [$schema], __METHOD__);
			if ($default) {
				$entity->push($default);
			}
			return $entity;
		}
	}
