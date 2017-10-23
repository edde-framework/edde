<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\Exception\SchemaReflectionException;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ISchemaReflectionService;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Object\Object;

		class SchemaReflectionService extends Object implements ISchemaReflectionService {
			use StringUtils;
			/**
			 * @var ISchema[]
			 */
			protected $schemaList = [];

			/**
			 * @inheritdoc
			 */
			public function getSchema(string $name): ISchema {
				return $this->schemaList[$name] ?? $this->schemaList[$name] = $this->reflect($name);
			}

			/**
			 * @param string $name
			 *
			 * @return ISchema
			 * @throws SchemaReflectionException
			 */
			protected function reflect(string $name): ISchema {
				try {
					if (isset($this->schemaList[$name])) {
						return $this->schemaList[$name];
					}
					$reflectionClass = new \ReflectionClass($name);
					$schema = Schema::create($name);
					foreach ($reflectionClass->getMethods() as $reflectionMethod) {
						if (($doc = $reflectionMethod->getDocComment()) === false || strpos($doc, '@schema') === false) {
							continue;
						}
						$attr = $this->stringUtils->match($doc, '~@schema\s*(?<attr>.*?)[\n\r]~sm', true);
						$attr = $attr['attr'] ?? '';
						$property = $schema->property($name = $reflectionMethod->getName());
						$property->type('string');
						if (strpos($attr, 'unique') !== false) {
							$property->unique();
						}
						if (strpos($attr, 'primary') !== false) {
							$property->primary();
							$property->generator($name);
						}
						if (($generator = $this->stringUtils->match($doc, '~@generator\s*(?<generator>.*?)[\n\r]~sm', true)) !== null) {
							$property->generator(trim($generator['generator']));
						}
						if (($type = $reflectionMethod->getReturnType()) !== null) {
							$property->type($type->getName());
							$property->required($type->allowsNull() === false);
						}
					}
					return $this->schemaList[$schema->getName()] = $schema;
				} catch (\Throwable $throwable) {
					throw new SchemaReflectionException(sprintf('Cannot do reflection of [%s]. Name is not probably a class.', $name));
				}
			}
		}
