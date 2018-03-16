<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\IRecord;
	use Edde\Exception\Entity\RecordException;
	use Edde\Inject\Entity\EntityManager;
	use Edde\Object;
	use Edde\Schema\ISchema;

	class Record extends Object implements IRecord {
		use EntityManager;
		/** @var \Edde\Schema\ISchema[] */
		protected $schemas;
		/** @var array */
		protected $source;
		/** @var IEntity[] */
		protected $entities;

		/**
		 * @param \Edde\Schema\ISchema[] $schemas
		 * @param array                  $source
		 */
		public function __construct(array $schemas, array $source) {
			$this->schemas = $schemas;
			$this->source = $source;
		}

		/** @inheritdoc */
		public function getSource(string $alias): array {
			if (isset($this->source[$alias]) === false) {
				throw new RecordException(sprintf('Requested unknown source alias [%s].', $alias));
			}
			return $this->source[$alias];
		}

		/** @inheritdoc */
		public function getEntity(string $alias): IEntity {
			if (isset($this->entities[$alias])) {
				return $this->entities[$alias];
			}
			if (isset($this->schemas[$alias]) === false) {
				throw new RecordException(sprintf('Requested unknown schema alias [%s].', $alias));
			}
			return $this->entities[$alias] = $this->entityManager->load($this->schemas[$alias], $this->getSource($alias));
		}
	}
