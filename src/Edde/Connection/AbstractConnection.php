<?php
	declare(strict_types=1);
	namespace Edde\Connection;

	use Edde\Object;
	use Edde\Query\IQuery;
	use Edde\Query\NativeQuery;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Storage\INativeQuery;
	use Edde\Storage\Query\Fragment\IWhereGroup;
	use Edde\Storage\Query\IFragment;
	use ReflectionClass;
	use ReflectionException;
	use ReflectionMethod;

	abstract class AbstractConnection extends Object implements IConnection {
		use ConfigService;
		use SchemaManager;
		/** @var string */
		protected $config;
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

		/**
		 * @param IFragment $fragment
		 *
		 * @return INativeQuery
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
		 * @return INativeQuery
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
	}
