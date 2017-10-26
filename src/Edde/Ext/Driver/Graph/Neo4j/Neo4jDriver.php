<?php
	namespace Edde\Ext\Driver\Graph\Neo4j;

		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\INativeQuery;
		use Edde\Common\Driver\AbstractDriver;
		use GraphAware\Bolt\GraphDatabase;
		use GraphAware\Bolt\Protocol\SessionInterface;

		class Neo4jDriver extends AbstractDriver {
			/** @var string */
			protected $url;
			/**
			 * @var SessionInterface
			 */
			protected $session;

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
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IDriver {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): IDriver {
				return $this;
			}

			protected function handleSetup(): void {
				parent::handleSetup();
				$this->session = GraphDatabase::driver($this->url)->session();
			}
		}
