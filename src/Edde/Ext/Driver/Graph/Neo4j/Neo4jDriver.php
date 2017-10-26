<?php
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\INativeQuery;
		use Edde\Common\Driver\AbstractDriver;
		use GraphAware\Bolt\GraphDatabase;
		use GraphAware\Bolt\Protocol\SessionInterface;
		use GraphAware\Bolt\Protocol\V1\Transaction;

		class Neo4jDriver extends AbstractDriver {
			/** @var string */
			protected $url;
			/**
			 * @var SessionInterface
			 */
			protected $session;
			/**
			 * @var Transaction
			 */
			protected $transaction;

			/**
			 * @param string $url
			 */
			public function __construct(string $url) {
				$this->url = $url;
			}

			/**
			 * @inheritdoc
			 */
			public function execute(INativeQuery $nativeQuery) {
				return $this->session->run($nativeQuery->getQuery(), $nativeQuery->getParameterList());
			}

			/**
			 * @inheritdoc
			 */
			public function start(): IDriver {
				($this->transaction = $this->session->transaction())->begin();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IDriver {
				$this->transaction->commit();
				$this->transaction = null;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): IDriver {
				$this->transaction->rollback();
				$this->transaction = null;
				return $this;
			}

			protected function handleSetup(): void {
				parent::handleSetup();
				$this->session = GraphDatabase::driver($this->url)->session();
			}
		}
