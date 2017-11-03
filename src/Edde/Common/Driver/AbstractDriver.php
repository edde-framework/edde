<?php
	declare(strict_types=1);
	namespace Edde\Common\Driver;

		use Edde\Api\Driver\Exception\DriverException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\ICrateSchemaQuery;
		use Edde\Api\Query\IQuery;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Object\Object;
		use ReflectionClass;
		use ReflectionMethod;

		abstract class AbstractDriver extends Object implements IDriver {
			use StringUtils;
			/**
			 * @var callable[]
			 */
			protected $executeList;

			/**
			 * @inheritdoc
			 */
			public function execute(IQuery $query) {
				if (isset($this->executeList[$execute = ('execute' . ($class = $this->stringUtils->extract(get_class($query))))]) === false) {
					throw new DriverException(sprintf('Unknown query type [%s] for driver [%s]: an [%s] executor is not implemented.', $class, static::class, $execute));
				}
				return $this->executeList[$execute]($query);
			}

			protected function executeCreateSchemaQuery(ICrateSchemaQuery $crateSchemaQuery) {
				if (($schema = $crateSchemaQuery->getSchema())->isRelation()) {
					return;
				}
				$primaryList = null;
				$indexList = null;
				$delimited = $this->delimite($schema->getName());
				foreach ($schema->getPropertyList() as $property) {
					$name = $property->getName();
					$fragment = 'n.' . $this->delimite($name);
					if ($property->isPrimary()) {
						$primaryList[] = $fragment;
					} else if ($property->isUnique()) {
						$this->native('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT ' . $fragment . ' IS UNIQUE');
					}
					if ($property->isRequired()) {
						$this->native('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT exists(' . $fragment . ')');
					}
				}
				if ($indexList) {
					$this->native('CREATE INDEX ON :' . $delimited . '(' . implode(',', $indexList) . ')');
				}
				if ($primaryList) {
					$this->native('CREATE CONSTRAINT ON (n:' . $delimited . ') ASSERT (' . implode(', ', $primaryList) . ') IS NODE KEY');
				}
			}

			/**
			 * @inheritdoc
			 */
			public function delimite(string $delimite): string {
				return '`' . str_replace('`', '``', $delimite) . '`';
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
