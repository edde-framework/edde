<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Filter\Inject\FilterManager;
		use Edde\Api\Generator\Inject\GeneratorManager;
		use Edde\Api\Sanitizer\Inject\SanitizerManager;
		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ISchemaLoader;
		use Edde\Api\Schema\ISchemaManager;
		use Edde\Common\Object\Object;

		class SchemaManager extends Object implements ISchemaManager {
			use GeneratorManager;
			use FilterManager;
			use SanitizerManager;
			/**
			 * @var ISchemaLoader[]
			 */
			protected $schemaLoaderList = [];
			/**
			 * @var ISchema[]
			 */
			protected $schemaList = [];

			/**
			 * @inheritdoc
			 */
			public function registerSchemaLoader(ISchemaLoader $schemaLoader): ISchemaManager {
				$this->schemaLoaderList[] = $schemaLoader;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function registerSchema(ISchema $schema): ISchemaManager {
				$this->schemaList[$schema->getName()] = $schema;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function registerSchemaList(array $schemaList): ISchemaManager {
				foreach ($schemaList as $schema) {
					$this->registerSchema($schema);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function load(string $name): ISchema {
				if (isset($this->schemaList[$name])) {
					return $this->schemaList[$name];
				}
				$schema = null;
				foreach ($this->schemaLoaderList as $schemaLoader) {
					if ($schema = $schemaLoader->getSchema($name)) {
						break;
					}
				}
				if ($schema === null) {
					throw new UnknownSchemaException(sprintf('Requested unknown schema [%s].', $name));
				}
				foreach ($schema->getPropertyList() as $property) {
					if ($property->isLink() === false) {
						continue;
					}
					$link = $property->getLinkNode();
//					new Link(Schema, Property, Propert);
//					$schema->link($target = $this->load($link->getAttribute('schema')), $link);
//					$target->linkTo($schema, $link);
				}
				return $this->schemaList[$name] = $schema;
			}

			/**
			 * @inheritdoc
			 */
			public function generate(string $schema, array $source): array {
				$schema = $this->load($schema);
				$result = $source;
				foreach ($schema->getPropertyList() as $property) {
					if (isset($source[$name = $property->getName()]) === false && ($generator = $property->getGenerator())) {
						$result[$name] = $this->generatorManager->getGenerator($generator)->generate();
					}
				}
				return $result;
			}

			/**
			 * @inheritdoc
			 */
			public function filter(string $schema, array $source): array {
				$schema = $this->load($schema);
				$result = $source;
				foreach ($source as $k => $v) {
					if ($filter = $schema->getProperty($k)->getFilter()) {
						$result[$k] = $this->filterManager->getFilter($filter)->filter($v);
					}
				}
				return $result;
			}

			/**
			 * @inheritdoc
			 */
			public function sanitize(string $schema, array $source): array {
				$schema = $this->load($schema);
				$result = $source;
				foreach ($source as $k => $v) {
					if ($sanitizer = $schema->getProperty($k)->getSanitizer()) {
						$result[$k] = $this->sanitizerManager->getSanitizer($sanitizer)->sanitize($v);
					}
				}
				return $result;
			}
		}
