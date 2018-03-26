<?php
	declare(strict_types=1);
	namespace Edde\Connection;

	use Edde\Object;
	use Edde\Query\Fragment\IWhereGroup;
	use Edde\Query\IFragment;
	use Edde\Query\INativeQuery;
	use Edde\Query\IQuery;
	use Edde\Query\NativeQuery;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Schema\SchemaManager;
	use ReflectionClass;
	use ReflectionException;
	use ReflectionMethod;
	use Throwable;

	abstract class AbstractConnection extends Object implements IConnection {
		use ConfigService;
		use SchemaManager;
		/** @var string */
		protected $config;
		/** @var int */
		protected $transaction = 0;
		/** @var callable[] */
		protected $executors;
		/** @var callable[] */
		protected $fragments;

		/**
		 * @param string $config
		 */
		public function __construct(string $config) {
			$this->config = $config;
		}

		/** @inheritdoc */
		public function execute(IQuery $query) {
			if (isset($this->executors[$name = ('execute' . ($class = substr($class = get_class($query), strrpos($class, '\\') + 1)))]) === false) {
				throw new ConnectionException(sprintf('Unknown query type [%s] for driver [%s]: an [%s] executor is not implemented.', $class, static::class, $name));
			}
			return $this->executors[$name]($query);
		}

		/** @inheritdoc */
		public function start(): IConnection {
			if ($this->transaction > 0) {
				$this->transaction++;
			}
			$this->onStart();
			$this->transaction++;
			return $this;
		}

		/** @inheritdoc */
		public function commit(): IConnection {
			if ($this->transaction === 0) {
				throw new TransactionException('Cannot commit a transaction - there is no one running!');
			} else if ($this->transaction === 1) {
				$this->onCommit();
			}
			/**
			 * it's intentional to lower the number of transaction after commit as a driver could throw an
			 * exception, thus transaction state could not be consistent
			 */
			$this->transaction--;
			return $this;
		}

		/** @inheritdoc */
		public function rollback(): IConnection {
			if ($this->transaction === 0) {
				throw new TransactionException('Cannot rollback a transaction - there is no one running!');
			} else if ($this->transaction === 1) {
				$this->onRollback();
			}
			$this->transaction--;
			return $this;
		}

		/** @inheritdoc */
		public function transaction(callable $callback) {
			$this->start();
			try {
				$result = $callback();
				$this->commit();
				return $result;
			} catch (Throwable $exception) {
				$this->rollback();
				throw $exception;
			}
		}

		/**
		 * @param IFragment $fragment
		 *
		 * @return \Edde\Query\INativeQuery
		 * @throws ConnectionException
		 */
		protected function fragment(IFragment $fragment): INativeQuery {
			if (isset($this->fragments[$name = ('fragment' . ($class = substr($class = get_class($fragment), strrpos($class, '\\') + 1)))]) === false) {
				throw new ConnectionException(sprintf('Unknown fragment type [%s] for driver [%s]: a [%s] fragment is not implemented.', $class, static::class, $name));
			}
			return $this->fragments[$name]($fragment);
		}

		/**
		 * @param IWhereGroup $whereGroup
		 *
		 * @return \Edde\Query\INativeQuery
		 * @throws ConnectionException
		 */
		protected function fragmentWhereGroup(IWhereGroup $whereGroup): INativeQuery {
			$group = null;
			$params = [];
			foreach ($whereGroup as $where) {
				$fragment = ' ';
				if ($group) {
					$fragment = ' ' . strtoupper($where->getRelation());
				}
				$group .= $fragment . ($query = $this->fragment($where))->getQuery();
				$params = array_merge($params, $query->getParams());
			}
			return new NativeQuery($group, $params);
		}

		/**
		 * @inheritdoc
		 *
		 * @throws ReflectionException
		 */
		protected function handleSetup(): void {
			parent::handleSetup();
			foreach ((new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PROTECTED) as $reflectionMethod) {
				if (strpos($name = $reflectionMethod->getName(), 'execute') !== false && strlen($name) > 7) {
					$this->executors[$name] = [
						$this,
						$name,
					];
				} else if (strpos($name, 'fragment') !== false && strlen($name) > 8) {
					$this->fragments[$name] = [
						$this,
						$name,
					];
				}
			}
		}

		abstract protected function onStart(): void;

		abstract protected function onCommit(): void;

		abstract protected function onRollback(): void;
	}
