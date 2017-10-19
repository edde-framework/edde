<?php
	namespace Edde\Common\Database;

		use Edde\Api\Database\Exception\EngineNotAvailableException;
		use PDO;

		abstract class AbstractPdoEngine extends AbstractEngine {
			/**
			 * @var string
			 */
			protected $dsn;
			/**
			 * @var \PDO
			 */
			protected $pdo;

			public function __construct(string $dsn) {
				$this->dsn = $dsn;
			}

			/**
			 * @throws EngineNotAvailableException
			 */
			public function handleSetup(): void {
				parent::handleSetup();
				if (extension_loaded('pdo_pgsql') === false) {
					throw new EngineNotAvailableException('PostgreSQL PDO is not available, oops!');
				}
				$this->pdo = new PDO($this->dsn);
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
				$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
				$this->pdo->setAttribute(PDO::ATTR_TIMEOUT, 120);
			}
		}
