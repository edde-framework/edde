<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

	use Edde\Api\Entity\Exception\RecordException;
	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\Inject\EntityManager;
	use Edde\Api\Entity\IRecord;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Object\Object;

	class Record extends Object implements IRecord {
		use EntityManager;
		/** @var ISchema[] */
		protected $schemas;
		/** @var array */
		protected $source;
		/** @var IEntity[] */
		protected $entities;

		/**
		 * @param ISchema[] $schemas
		 * @param array     $source
		 */
		public function __construct(array $schemas, array $source) {
			$this->schemas = $schemas;
			$this->source = $source;
		}

		/** @inheritdoc */
		public function getEntity(string $alias): IEntity {
			if (isset($this->entities[$alias])) {
				return $this->entities[$alias];
			}
			if (isset($this->schemas[$alias]) === false) {
				throw new RecordException(sprintf('Requested unknown schema alias [%s].', $alias));
			} else if (isset($this->source[$alias]) === false) {
				throw new RecordException(sprintf('Requested unknown source alias [%s].', $alias));
			}
			return $this->entities[$alias] = $this->entityManager->load($this->schemas[$alias], $this->source[$alias]);
		}
	}