<?php
	namespace Edde\Ext\Storage\Neo4j;

		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\IEntity;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Storage\AbstractStorage;
		use GraphAware\Bolt\GraphDatabase;
		use GraphAware\Bolt\Protocol\SessionInterface;

		class Neo4jStorage extends AbstractStorage {
			/**
			 * @var SessionInterface
			 */
			protected $session;

			/**
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
			}

			/**
			 * @inheritdoc
			 */
			public function query($query, array $parameterList = []) {
			}

			/**
			 * @inheritdoc
			 */
			public function native(INativeQuery $nativeQuery) {
				return $this->session->run($nativeQuery->getQuery(), $nativeQuery->getParameterList());
			}

			/**
			 * @inheritdoc
			 */
			public function start(bool $exclusive = false): IStorage {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IStorage {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): IStorage {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function save(IEntity $entity): IStorage {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function insert(IEntity $entity): IStorage {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function push(string $schema, array $source): IEntity {
			}

			/**
			 * @inheritdoc
			 */
			public function update(IEntity $entity): IStorage {
				return $this;
			}

			protected function connect(): IStorage {
				$this->session = GraphDatabase::driver('bolt://172.17.0.1')->session();
				return $this;
			}

			protected function handleSetup(): void {
				parent::handleSetup();
				$this->connect();
			}
		}
