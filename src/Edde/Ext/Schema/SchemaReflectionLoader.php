<?php
	namespace Edde\Ext\Schema;

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
			 */
			protected function reflect(string $schema): ISchema {
				try {
					if (isset($this->schemaList[$schema])) {
						return $this->schemaList[$schema];
					}
					$reflectionClass = new \ReflectionClass($schema);
					$schemaBuilder = new SchemaBuilder($schema);
					$doc = ($doc = $reflectionClass->getDocComment()) ? $doc : '';
					$schemaBuilder->relation(strpos($doc, '@relation') !== false);
					foreach ($reflectionClass->getMethods() as $reflectionMethod) {
						if (($doc = $reflectionMethod->getDocComment()) === false) {
							continue;
						} else if (strpos($doc, '@schema') !== false) {
							$attr = $this->stringUtils->match($doc, '~@schema\s*(?<attr>.*?)[\n\r]~sm', true);
							$attr = $attr['attr'] ?? '';
							$propertyBuilder = $schemaBuilder->property($name = $reflectionMethod->getName());
							$propertyBuilder->type($propertyType = 'string');
							if (strpos($attr, 'unique') !== false) {
								$propertyBuilder->unique();
							}
							if (strpos($attr, 'primary') !== false) {
								$propertyBuilder->primary();
								$propertyBuilder->generator($name);
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
							if (($generator = $this->stringUtils->match($doc, '~@generator\s*(?<value>.*?)[\n\r]~sm', true)) !== null) {
								$propertyBuilder->generator(trim($generator['value']));
							}
							if (($filter = $this->stringUtils->match($doc, '~@filter\s*(?<value>.*?)[\n\r]~sm', true)) !== null) {
								$propertyBuilder->filter(trim($filter['value']));
							}
							if (($sanitizer = $this->stringUtils->match($doc, '~@sanitizer\s*(?<value>.*?)[\n\r]~sm', true)) !== null) {
								$propertyBuilder->sanitizer(trim($sanitizer['value']));
							}
						}
					}
					return $this->schemaList[$schema] = $schemaBuilder->getSchema();
				} catch (\Throwable $throwable) {
					throw new SchemaReflectionException(sprintf('Cannot do reflection of [%s]. Name is not probably a class.', $schema), 0, $throwable);
				}
			}
		}
