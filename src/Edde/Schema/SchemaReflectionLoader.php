<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Service\Utils\StringUtils;
	use ReflectionClass;
	use Throwable;
	use function str_replace;

	class SchemaReflectionLoader extends AbstractSchemaLoader implements ISchemaLoader {
		use StringUtils;
		/** @var ISchemaBuilder[] */
		protected $schemaBuilders = [];

		/** @inheritdoc */
		public function getSchemaBuilder(string $schema): ISchemaBuilder {
			return $this->schemaBuilders[$schema] ?? $this->schemaBuilders[$schema] = $this->reflect($schema);
		}

		/**
		 * @param string $schema
		 *
		 * @return ISchemaBuilder
		 *
		 * @throws SchemaException
		 */
		protected function reflect(string $schema): ISchemaBuilder {
			try {
				if (isset($this->schemaBuilders[$schema])) {
					return $this->schemaBuilders[$schema];
				}
				$reflectionClass = new ReflectionClass($schema);
				$schemaBuilder = new SchemaBuilder($schema);
				$methodCount = 0;
				$primary = false;
				$isRelation = false;
				foreach ($reflectionClass->getConstants() as $name => $value) {
					switch ($name) {
						case 'alias':
							if ($value === true) {
								$value = str_replace('-schema', '', $this->stringUtils->recamel($this->stringUtils->extract($schema), '-'));
							}
							$schemaBuilder->alias($value);
							break;
						case 'relation':
							$schemaBuilder->relation($isRelation = $value);
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
									throw new SchemaException(sprintf('Schema [%s] has another primary key [%s]; composite primary keys are not allowed!', $schema, $propertyName));
								}
								$primary = true;
								$propertyBuilder->primary();
								$propertyBuilder->generator($propertyName);
								break;
							case 'generator':
								if (($generator = $parameter->getDefaultValue()) === null || is_string($generator) === false) {
									throw new SchemaException(sprintf('Parameter [%s::%s($generator)] must have default a string value as a generator name.', $schema, $propertyName));
								}
								$propertyBuilder->generator($generator);
								break;
							case 'filter':
								if (($filter = $parameter->getDefaultValue()) === null || is_string($filter) === false) {
									throw new SchemaException(sprintf('Parameter [%s::%s($filter)] must have a default string value as a filter name.', $schema, $propertyName));
								}
								$propertyBuilder->filter($filter);
								break;
							case 'sanitizer':
								if (($sanitizer = $parameter->getDefaultValue()) === null || is_string($sanitizer) === false) {
									throw new SchemaException(sprintf('Parameter [%s::%s($sanitizer)] must have a default string value as a sanitizer name.', $schema, $propertyName));
								}
								$propertyBuilder->sanitizer($sanitizer);
								break;
							case 'validator':
								if (($validator = $parameter->getDefaultValue()) === null || is_string($validator) === false) {
									throw new SchemaException(sprintf('Parameter [%s::%s($validator)] must have a default string value as a validator name.', $schema, $propertyName));
								}
								$propertyBuilder->validator($validator);
								break;
							case 'default':
								$propertyBuilder->default($parameter->getDefaultValue());
								break;
						}
					}
					if (($type = $reflectionMethod->getReturnType()) !== null) {
						$propertyBuilder->type($propertyType = $type->getName());
						$propertyBuilder->required($type->allowsNull() === false);
					}
					if ($reflectionMethod->getNumberOfParameters() === 1 && ($parameter = $reflectionMethod->getParameters()[0]) && ($type = $parameter->getType())) {
						$propertyBuilder->link();
						$schemaBuilder->link(new LinkBuilder($isRelation ? $schema : $propertyName, $schema, $propertyName, $type->getName(), $parameter->getName()));
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
				return $this->schemaBuilders[$schema] = $schemaBuilder;
			} catch (SchemaException $exception) {
				throw $exception;
			} catch (Throwable $throwable) {
				throw new SchemaException(sprintf('Cannot do reflection of [%s]. Name is not probably a class.', $schema), 0, $throwable);
			}
		}
	}
