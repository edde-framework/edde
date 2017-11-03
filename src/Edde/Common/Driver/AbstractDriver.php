<?php
	declare(strict_types=1);
	namespace Edde\Common\Driver;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\IDriver;
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
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
				if (isset($this->executeList[$execute = ('execute' . ($class = substr($class = get_class($query), strrpos($class, '\\') + 1)))]) === false) {
					throw new DriverException(sprintf('Unknown query type [%s] for driver [%s]: an [%s] executor is not implemented.', $class, static::class, $execute));
				}
				return $this->executeList[$execute]($query);
			}

			protected function handleSetup(): void {
				parent::handleSetup();
				foreach ((new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PROTECTED) as $reflectionMethod) {
					if (strpos($name = $reflectionMethod->getName(), 'execute') !== false && strlen($name) > 7) {
						$this->executeList[$name] = [
							$this,
							$name,
						];
					}
				}
			}
		}
