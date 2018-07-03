<?php
	declare(strict_types=1);

	namespace Edde\Common\Schema;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\INodeQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaLoader;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Schema\SchemaManagerException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Filter\BoolFilter;
	use Edde\Common\Node\NodeQuery;
	use Edde\Common\Object;

	class SchemaManager extends Object implements ISchemaManager {
		use LazyContainerTrait;
		use ConfigurableTrait;
		/**
		 * @var ISchemaLoader[]
		 */
		protected $schemaLoaderList = [];
		/**
		 * @var INodeQuery
		 */
		protected $propertyListNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $propertyFilterNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $propertySetterFilterNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $propertyGetterFilterNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $collectionNodeQuery;
		/**
		 * @var INodeQuery
		 */
		protected $linkNodeQuery;
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
		public function createSchema(INode $node): ISchema {
			$schema = new Schema($node->getName(), $node->getAttribute('namespace'));
			$schema->setMetaList($node->getMetaList()->array());
			$magic = $schema->getMeta('magic', true);
			foreach ($this->propertyListNodeQuery->filter($node) as $propertyNode) {
				$schema->addProperty($property = new Property($schema, $propertyNode->getName(), str_replace('[]', '', $type = $propertyNode->getAttribute('type', 'string')), filter_var($propertyNode->getAttribute('required', true), FILTER_VALIDATE_BOOLEAN), filter_var($propertyNode->getAttribute('unique'), FILTER_VALIDATE_BOOLEAN), filter_var($propertyNode->getAttribute('identifier'), FILTER_VALIDATE_BOOLEAN), strpos($type, '[]') !== false));
				if (($generator = $propertyNode->getAttribute('generator')) !== null) {
					$property->setGenerator($this->container->create((string)$generator, [], __METHOD__));
				}
				$type = $property->getType();
				foreach ($this->propertyFilterNodeQuery->filter($propertyNode) as $filterNode) {
					$type = null;
					$property->addFilter($this->container->create($filterNode->getValue(), [], __METHOD__));
				}
				foreach ($this->propertySetterFilterNodeQuery->filter($propertyNode) as $filterNode) {
					$type = null;
					$property->addSetterFilter($this->container->create($filterNode->getValue(), [], __METHOD__));
				}
				foreach ($this->propertyGetterFilterNodeQuery->filter($propertyNode) as $filterNode) {
					$type = null;
					$property->addGetterFilter($this->container->create($filterNode->getValue(), [], __METHOD__));
				}
				/**
				 * magical things can be turned off
				 */
				if ($magic === false) {
					$type = null;
				}
				/**
				 * support for automagical type conversions
				 */
				switch ($type) {
					case 'bool':
						$property->addFilter(new BoolFilter());
						break;
				}
			}
			return $schema;
		}

		/**
		 * @inheritdoc
		 */
		public function getSchema(string $name): ISchema {
			if (isset($this->schemaList[$name]) === false) {
				throw new UnknownSchemaException(sprintf('Requested unknown schema [%s].', $name));
			}
			return $this->schemaList[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function getSchemaList(): array {
			return $this->schemaList;
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			parent::handleInit();
			$this->propertyListNodeQuery = new NodeQuery('/*/property-list/*');
			$this->propertyFilterNodeQuery = new NodeQuery('/*/property-list/*/filter/*');
			$this->propertySetterFilterNodeQuery = new NodeQuery('/*/property-list/*/setter-filter/*');
			$this->propertyGetterFilterNodeQuery = new NodeQuery('/*/property-list/*/getter-filter/*');
			$this->collectionNodeQuery = new NodeQuery('/*/collection/*');
			$this->linkNodeQuery = new NodeQuery('/*/link/*');
		}

		protected function getSchemaName(INode $schemaNode) {
			return (($namespace = $schemaNode->getAttribute('namespace')) ? ($namespace . '\\') : null) . $schemaNode->getName();
		}

		/**
		 * @inheritdoc
		 */
		protected function handleSetup() {
			parent::handleSetup();
			if (empty($this->schemaLoaderList)) {
				throw new SchemaManagerException(sprintf('There are no schema loaders in [%s].', static::class));
			}
			$nodeList = [];
			foreach ($this->schemaLoaderList as $schemaLoader) {
				$schemaLoader->setup();
				foreach ($schemaLoader->load() as $node) {
					$nodeList[] = $node;
					$schema = $this->createSchema($node);
					$this->schemaList[$schema->getSchemaName()] = $schema;
				}
			}
			foreach ($nodeList as $node) {
				$sourceSchema = $this->schemaList[$this->getSchemaName($node)];
				foreach ($this->collectionNodeQuery->filter($node) as $collectionNode) {
					if (isset($this->schemaList[$schemaName = $collectionNode->getAttribute('schema')]) === false) {
						throw new SchemaManagerException(sprintf('Cannot use collection to an unknown schema [%s].', $schemaName));
					}
					$targetSchema = $this->schemaList[$schemaName];
					$sourceSchema->collection($collectionNode->getName(), $sourceSchema->getProperty($collectionNode->getValue()), $targetSchema->getProperty((string)$collectionNode->getAttribute('property')));
				}
				foreach ($this->linkNodeQuery->filter($node) as $linkNode) {
					if (isset($this->schemaList[$schemaName = $linkNode->getAttribute('schema')]) === false) {
						throw new SchemaManagerException(sprintf('Cannot use link to an unknown schema [%s].', $schemaName));
					}
					$targetSchema = $this->schemaList[$schemaName];
					$sourceSchema->link($linkNode->getName(), $sourceSchema->getProperty($linkNode->getValue($linkNode->getName())), $targetSchema->getProperty((string)$linkNode->getAttribute('property')));
				}
			}
		}
	}
