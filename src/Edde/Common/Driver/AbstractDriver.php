<?php
	declare(strict_types=1);
	namespace Edde\Common\Driver;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\Fragment\IFragment;
		use Edde\Api\Query\INativeQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Common\Object\Object;
		use ReflectionClass;
		use ReflectionMethod;

		abstract class AbstractDriver extends Object implements IDriver {
			use SchemaManager;
			/**
			 * @var callable[]
			 */
			protected $executeList;
			/**
			 * @var callable[]
			 */
			protected $fragmentList;

			/**
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
				if (isset($this->executeList[$name = ('execute' . ($class = substr($class = get_class($query), strrpos($class, '\\') + 1)))]) === false) {
					throw new DriverException(sprintf('Unknown query type [%s] for driver [%s]: an [%s] executor is not implemented.', $class, static::class, $name));
				}
				return $this->executeList[$name]($query);
			}

			/**
			 * @param IFragment $fragment
			 *
			 * @return INativeQuery
			 * @throws DriverException
			 */
			protected function fragment(IFragment $fragment): INativeQuery {
				if (isset($this->fragmentList[$name = ('fragment' . ($class = substr($class = get_class($fragment), strrpos($class, '\\') + 1)))]) === false) {
					throw new DriverException(sprintf('Unknown fragment type [%s] for driver [%s]: a [%s] fragment is not implemented.', $class, static::class, $name));
				}
				return $this->fragmentList[$name]($fragment);
			}

			protected function handleSetup(): void {
				parent::handleSetup();
				foreach ((new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PROTECTED) as $reflectionMethod) {
					if (strpos($name = $reflectionMethod->getName(), 'execute') !== false && strlen($name) > 7) {
						$this->executeList[$name] = [
							$this,
							$name,
						];
					} else if (strpos($name, 'fragment') !== false && strlen($name) > 8) {
						$this->fragmentList[$name] = [
							$this,
							$name,
						];
					}
				}
			}
		}
