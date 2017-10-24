<?php
	namespace Edde\Ext\Schema;

		use Edde\Api\Schema\Exception\SchemaReflectionException;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ISchemaLoader;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Schema\AbstractSchemaLoader;
		use Edde\Common\Schema\Schema;

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
			 */
			protected function reflect(string $schema): ISchema {
				try {
					if (isset($this->schemaList[$schema])) {
						return $this->schemaList[$schema];
					}
					$reflectionClass = new \ReflectionClass($schema);
					$schema = Schema::create($schema);
					foreach ($reflectionClass->getMethods() as $reflectionMethod) {
						if (($doc = $reflectionMethod->getDocComment()) === false) {
							continue;
						} else if (strpos($doc, '@schema') !== false) {
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
							if (($type = $reflectionMethod->getReturnType()) !== null) {
								$property->type($type->getName());
								$property->required($type->allowsNull() === false);
							}
							switch ($type = $property->getType()) {
								case 'float':
								case 'int':
								case 'bool':
								case 'datetime':
								case \DateTime::class:
									$property->filter($type);
									$property->sanitizer($type);
									break;
							}
							if (($generator = $this->stringUtils->match($doc, '~@generator\s*(?<value>.*?)[\n\r]~sm', true)) !== null) {
								$property->generator(trim($generator['value']));
							}
							if (($filter = $this->stringUtils->match($doc, '~@filter\s*(?<value>.*?)[\n\r]~sm', true)) !== null) {
								$property->filter(trim($filter['value']));
							}
							if (($sanitizer = $this->stringUtils->match($doc, '~@sanitizer\s*(?<value>.*?)[\n\r]~sm', true)) !== null) {
								$property->sanitizer(trim($sanitizer['value']));
							}
						} else if (strpos($doc, '@relation') !== false && ($type = $reflectionMethod->getReturnType()) !== null) {
							$attr = $this->stringUtils->match($doc, '~@relation\s*(?<attr>.*?)[\n\r]~sm', true);
							$schema->relation($reflectionMethod->getName(), $attr['attr'], $type->getName());
						}
					}
					return $this->schemaList[$schema->getName()] = $schema;
				} catch (\Throwable $throwable) {
					throw new SchemaReflectionException(sprintf('Cannot do reflection of [%s]. Name is not probably a class.', $schema));
				}
			}
		}
