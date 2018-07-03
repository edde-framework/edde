<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\INodeQuery;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\SchemaFactoryException;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Filter\BoolFilter;
	use Edde\Common\Node\NodeQuery;

	class SchemaFactory extends AbstractDeffered implements ISchemaFactory {
		use LazyContainerTrait;
		use LazyResourceManagerTrait;
		/**
		 * @var INode[]
		 */
		protected $schemaNodeList = [];
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

		public function load(string $file): INode {
			$this->addSchemaNode($node = $this->resourceManager->file($file));
			return $node;
		}

		public function addSchemaNode(INode $node) {
			$this->schemaNodeList[$this->getSchemaName($node)] = $node;
			return $this;
		}

		protected function getSchemaName(INode $schemaNode) {
			return (($namespace = $schemaNode->getAttribute('namespace')) ? ($namespace . '\\') : null) . $schemaNode->getName();
		}

		public function create() {
			$this->use();
			/** @var $schemaList ISchema[] */
			$schemaList = [];
			foreach ($this->schemaNodeList as $schemaNode) {
				$schema = $this->createSchema($schemaNode);
				$schemaList[$schema->getSchemaName()] = $schema;
			}
			foreach ($this->schemaNodeList as $schemaNode) {
				$sourceSchema = $schemaList[$this->getSchemaName($schemaNode)];
				foreach ($this->collectionNodeQuery->filter($schemaNode) as $collectionNode) {
					if (isset($schemaList[$schemaName = $collectionNode->getAttribute('schema')]) === false) {
						throw new SchemaFactoryException(sprintf('Cannot use collection to an unknown schema [%s].', $schemaName));
					}
					$targetSchema = $schemaList[$schemaName];
					$sourceSchema->collection($collectionNode->getName(), $sourceSchema->getProperty($collectionNode->getValue()), $targetSchema->getProperty($collectionNode->getAttribute('property')));
				}
				foreach ($this->linkNodeQuery->filter($schemaNode) as $linkNode) {
					if (isset($schemaList[$schemaName = $linkNode->getAttribute('schema')]) === false) {
						throw new SchemaFactoryException(sprintf('Cannot use link to an unknown schema [%s].', $schemaName));
					}
					$targetSchema = $schemaList[$schemaName];
					$sourceSchema->link($linkNode->getName(), $sourceSchema->getProperty($linkNode->getValue($linkNode->getName())), $targetSchema->getProperty($linkNode->getAttribute('property')));
				}
			}
			return $schemaList;
		}

		protected function createSchema(INode $schemaNode) {
			$schema = new Schema($schemaNode->getName(), $schemaNode->getAttribute('namespace'));
			$schema->setMetaList($schemaNode->getMetaList());
			$magic = $schema->getMeta('magic', true);
			foreach ($this->propertyListNodeQuery->filter($schemaNode) as $propertyNode) {
				$schema->addProperty($property = new SchemaProperty($schema, $propertyNode->getName(), str_replace('[]', '', $type = $propertyNode->getAttribute('type', 'string')), filter_var($propertyNode->getAttribute('required', true), FILTER_VALIDATE_BOOLEAN), filter_var($propertyNode->getAttribute('unique'), FILTER_VALIDATE_BOOLEAN), filter_var($propertyNode->getAttribute('identifier'), FILTER_VALIDATE_BOOLEAN), strpos($type, '[]') !== false));
				if (($generator = $propertyNode->getAttribute('generator')) !== null) {
					$property->setGenerator($this->container->create($generator));
				}
				$type = $property->getType();
				foreach ($this->propertyFilterNodeQuery->filter($propertyNode) as $filterNode) {
					$type = null;
					$property->addFilter($this->container->create($filterNode->getValue()));
				}
				foreach ($this->propertySetterFilterNodeQuery->filter($propertyNode) as $filterNode) {
					$type = null;
					$property->addSetterFilter($this->container->create($filterNode->getValue()));
				}
				foreach ($this->propertyGetterFilterNodeQuery->filter($propertyNode) as $filterNode) {
					$type = null;
					$property->addGetterFilter($this->container->create($filterNode->getValue()));
				}
				/** @noinspection DisconnectedForeachInstructionInspection */
				/**
				 * magicall thing can be turned off
				 */
				if ($magic === false) {
					$type = null;
				}
				/**
				 * support for automagical type convertions
				 */
				switch ($type) {
					case 'bool':
						$property->addFilter(new BoolFilter());
						break;
				}
			}
			return $schema;
		}

		protected function prepare() {
			$this->propertyListNodeQuery = new NodeQuery('/*/property-list/*');
			$this->propertyFilterNodeQuery = new NodeQuery('/*/property-list/*/filter/*');
			$this->propertySetterFilterNodeQuery = new NodeQuery('/*/property-list/*/setter-filter/*');
			$this->propertyGetterFilterNodeQuery = new NodeQuery('/*/property-list/*/getter-filter/*');
			$this->collectionNodeQuery = new NodeQuery('/*/collection/*');
			$this->linkNodeQuery = new NodeQuery('/*/link/*');
		}
	}
