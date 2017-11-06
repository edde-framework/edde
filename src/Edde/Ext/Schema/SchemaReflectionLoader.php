<?php
	declare(strict_types=1);
	namespace Edde\Ext\Schema;

		use Edde\Api\Schema\Exception\MultiplePrimaryException;
		use Edde\Api\Schema\Exception\SchemaException;
		use Edde\Api\Schema\Exception\SchemaReflectionException;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ISchemaLoader;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Schema\AbstractSchemaLoader;
		use Edde\Common\Schema\SchemaBuilder;

		class SchemaReflectionLoader extends AbstractSchemaLoader implements ISchemaLoader {
			use StringUtils;
			/**
			 * @var ISchema[]
			 */
			protected $schemaList = [];

			/**
			 * @inheritdoc
			 */
			public function getSchema(string $schema): ISchema {
				return $this->schemaList[$schema] ?? $this->schemaList[$schema] = $this->reflect($schema);
			}

			/**
			 * @param string $schema
			 *
			 * @return ISchema
			 * @throws SchemaReflectionException
			 * @throws SchemaException
			 */
			protected function reflect(string $schema): ISchema {
				try {
					if (isset($this->schemaList[$schema])) {
						return $this->schemaList[$schema];
					}
					$reflectionClass = new \ReflectionClass($schema);
					$schemaBuilder = new SchemaBuilder($schema);
					$methodCount = 0;
					$primary = false;
					foreach ($reflectionClass->getConstants() as $name => $value) {
						switch ($name) {
							case 'alias':
								$schemaBuilder->alias($value);
								break;
							case 'relation':
								$schemaBuilder->relation($value);
								break;
						}
					}
					foreach ($reflectionClass->getMethods() as $reflectionMethod) {
						$methodCount++;
						$propertyBuilder = $schemaBuilder->property($propertyName = $reflectionMethod->getName());
						$propertyBuilder->type($propertyType = 'string');
						foreach ($reflectionMethod->getParameters() as $parameter) {
							switch ($parameter->getName()) {
								case 'unique':
									$propertyBuilder->unique();
									break;
								case 'primary':
									if ($primary) {
										throw new MultiplePrimaryException(sprintf('Schema [%s] has another primary key [%s]; composite primary keys are not allowed!', $schema, $propertyName));
									}
									$primary = true;
									$propertyBuilder->primary();
									$propertyBuilder->generator($propertyName);
									break;
								case 'generator':
									if (($generator = $parameter->getDefaultValue()) || is_string($generator) === false) {
										throw new SchemaReflectionException(sprintf('Parameter [%s::%s($generator)] must have string default value as a generator name.', $schema, $propertyName));
									}
									$propertyBuilder->generator($generator);
									break;
								case 'filter':
									if (($filter = $parameter->getDefaultValue()) || is_string($filter) === false) {
										throw new SchemaReflectionException(sprintf('Parameter [%s::%s($filter)] must have string default value as a filter name.', $schema, $propertyName));
									}
									$propertyBuilder->filter($filter);
									break;
								case 'sanitizer':
									if (($sanitizer = $parameter->getDefaultValue()) || is_string($sanitizer) === false) {
										throw new SchemaReflectionException(sprintf('Parameter [%s::%s($sanitizer)] must have string default value as a sanitizer name.', $schema, $propertyName));
									}
									$propertyBuilder->sanitizer($sanitizer);
									break;
							}
						}
						if (($type = $reflectionMethod->getReturnType()) !== null) {
							$propertyBuilder->type($propertyType = $type->getName());
							$propertyBuilder->required($type->allowsNull() === false);
						}
						/**
						 * exactly one parameter means link to another schema
						 */
						if ($reflectionMethod->getNumberOfParameters() === 1) {
							list($parameter) = $reflectionMethod->getParameters();
							if ($type = $parameter->getType()) {
								$propertyBuilder->required($type->allowsNull() === false);
								$propertyBuilder->link($type->getName(), $parameter->getName());
							}
						}
						switch ($propertyType) {
							case 'float':
							case 'int':
							case 'bool':
							case 'datetime':
							case \DateTime::class:
								$propertyBuilder->filter($propertyType);
								$propertyBuilder->sanitizer($propertyType);
								break;
						}
					}
					if (($schema = $this->schemaList[$schema] = $schemaBuilder->getSchema())->hasAlias()) {
						$this->schemaList[$schema->getAlias()] = $schema;
					}
					return $schema;
				} catch (SchemaException $exception) {
					throw $exception;
				} catch (\Throwable $throwable) {
					throw new SchemaReflectionException(sprintf('Cannot do reflection of [%s]. Name is not probably a class.', $schema), 0, $throwable);
				}
			}
		}
