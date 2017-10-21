<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Schema\Exception\SchemaReflectionException;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ISchemaReflectionService;
		use Edde\Common\Object\Object;

		class SchemaReflectionService extends Object implements ISchemaReflectionService {
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
					$reflectionClass = new \ReflectionClass($name);
					$schema = Schema::create($reflectionClass->getName());
					foreach ($reflectionClass->getProperties() as $property) {
						$doc = $property->getDocComment();
					}
				} catch (\Throwable $throwable) {
					throw new SchemaReflectionException(sprintf('Cannot do reflection of [%s]. Name is not probably a class.', $name));
				}
			}
		}
