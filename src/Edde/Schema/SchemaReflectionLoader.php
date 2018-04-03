<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use DateTime;
	use Edde\Service\Utils\StringUtils;
	use ReflectionClass;
	use Throwable;
	use function is_string;
	use function str_replace;

	class SchemaReflectionLoader extends AbstractSchemaLoader implements ISchemaLoader {
		use StringUtils;
		/** @var ISchemaBuilder[] */
		protected $schemaBuilders = [];

		/** @inheritdoc */
		public function getSchemaBuilder(string $schema): ISchemaBuilder {
			try {
				if (isset($this->schemaBuilders[$schema])) {
					return $this->schemaBuilders[$schema];
				}
				$reflectionClass = new ReflectionClass($schema);
				$schemaBuilder = new SchemaBuilder($schema);
				$primary = false;
				foreach ($reflectionClass->getConstants() as $name => $value) {
					switch ($name) {
						case 'alias':
							/**
							 * auto alias support; name kick off "schema" from the name and rest is used as an alias
							 */
							if ($value === true) {
								$value = str_replace('-schema', '', $this->stringUtils->recamel($this->stringUtils->extract($schema), '-'));
							}
							$schemaBuilder->alias($value);
							break;
						case 'relation':
							/**
							 * mark a schema as a relational schema
							 */
							$schemaBuilder->relation($value);
							break;
						case 'primary':
							$primary = $value;
							break;
					}
				}
				if ($primary === false) {
					throw new SchemaException(sprintf('Schema [%s] has no primary property; please define one (you can extend [%s] to use default uuid support).', $schema, UuidSchema::class));
				}
				/**
				 * go through all methods as they're used as schema definition
				 */
				foreach ($reflectionClass->getMethods() as $reflectionMethod) {
					$propertyBuilder = $schemaBuilder->property($propertyName = $reflectionMethod->getName());
					/**
					 * set default property type to a string
					 */
					$propertyBuilder->type($propertyType = 'string');
					if (($type = $reflectionMethod->getReturnType()) !== null) {
						$propertyBuilder->type($propertyType = $type->getName());
						$propertyBuilder->required($type->allowsNull() === false);
					}
					if ($propertyName === $primary) {
						$primary = true;
						$propertyBuilder->primary();
						$propertyBuilder->generator($propertyName);
					}
					foreach ($reflectionMethod->getParameters() as $parameter) {
						switch ($parameter->getName()) {
							case 'unique':
								$propertyBuilder->unique();
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
							case 'type':
								if (($type = $parameter->getDefaultValue()) === null || is_string($type) === false) {
									throw new SchemaException(sprintf('Parameter [%s::%s($type)] must have a default string value as a type name.', $schema, $propertyName));
								}
								$propertyBuilder->type($type);
								$propertyBuilder->required($parameter->isOptional());
								break;
							case 'default':
								$propertyBuilder->default($parameter->getDefaultValue());
								break;
							default:
								throw new SchemaException(sprintf('Unknown schema directive [%s::%s].', $schema, $propertyName));
						}
					}
					switch ($propertyType) {
						case 'float':
						case 'int':
						case 'bool':
						case 'datetime':
						case DateTime::class:
							$propertyBuilder->filter($propertyType);
							$propertyBuilder->sanitizer($propertyType);
							$propertyBuilder->validator($propertyType);
							break;
					}
				}
				if ($primary !== true) {
					throw new SchemaException(sprintf('Primary property [%s::%s] is defined, but property does not exist; please add corresponding method to schema.', $schema, $primary));
				}
				return $this->schemaBuilders[$schema] = $schemaBuilder;
			} catch (SchemaException $exception) {
				throw $exception;
			} catch (Throwable $throwable) {
				throw new SchemaException(sprintf('Cannot do reflection of [%s]. Name is not probably a class.', $schema), 0, $throwable);
			}
		}
	}
