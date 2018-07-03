<?php
	declare(strict_types=1);

	namespace Edde\Common\Crate;

	use ArrayIterator;
	use Edde\Api\Crate\Exception\CrateException;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Object\Object;

	class Collection extends Object implements ICollection {
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var string
		 */
		protected $crate;
		/**
		 * @var ICrate[]
		 */
		protected $crateList = [];

		/**
		 * @param ICrateFactory $crateFactory
		 * @param string        $schema
		 * @param string        $crate
		 */
		public function __construct(ICrateFactory $crateFactory, string $schema, string $crate = null) {
			$this->crateFactory = $crateFactory;
			$this->schema = $schema;
			$this->crate = $crate;
		}

		/**
		 * @inheritdoc
		 */
		public function getSchema(): ISchema {
			return $this->schema;
		}

		/**
		 * @inheritdoc
		 */
		public function createCrate(array $push = null): ICrate {
			return $this->crateFactory->crate($this->schema, $push, $this->crate ?: $this->schema);
		}

		/**
		 * @inheritdoc
		 */
		public function addCrate(ICrate $crate): ICollection {
			$schema = $crate->getSchema();
			if ($schema->getSchemaName() !== $this->schema) {
				throw new CrateException(sprintf('Cannot add crate with different schema [%s] to the collection [%s].', $crate->getSchema()->getSchemaName(), $this->schema));
			}
			$this->crateList[] = $crate;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			return new ArrayIterator($this->crateList);
		}
	}
