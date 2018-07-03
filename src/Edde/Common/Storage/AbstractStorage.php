<?php
	declare(strict_types=1);

	namespace Edde\Common\Storage;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\Inject\CrateFactory;
	use Edde\Api\Query\IQuery;
	use Edde\Api\Schema\Inject\SchemaManager;
	use Edde\Api\Schema\SchemaException;
	use Edde\Api\Storage\EmptyResultException;
	use Edde\Api\Storage\IBoundQuery;
	use Edde\Api\Storage\ICollection;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Crate\Crate;
	use Edde\Common\Object\Object;
	use Edde\Common\Query\Select\SelectQuery;

	/**
	 * Base for all storage implementations.
	 */
	abstract class AbstractStorage extends Object implements IStorage {
		use SchemaManager;
		use CrateFactory;
		use Container;
		use ConfigurableTrait;

		/**
		 * @param string $query
		 * @param array  ...$parameterList
		 *
		 * @return IBoundQuery
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function bound(string $query, ...$parameterList): IBoundQuery {
			return (new BoundQuery())->bind($this->container->create($query, $parameterList, __METHOD__), $this);
		}

		/**
		 * @return IBoundQuery
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function query(): IBoundQuery {
			return $this->bound(SelectQuery::class);
		}

		/**
		 * @inheritdoc
		 * @throws SchemaException
		 */
		public function collectionTo(ICrate $crate, string $relation, string $source, string $target, string $crateTo = null): ICollection {
			$relationSchema = $this->schemaManager->getSchema($relation);
			$sourceLink = $relationSchema->getLink($source);
			$targetLink = $relationSchema->getLink($target);
			$targetSchema = $targetLink->getTarget()->getSchema();
			$targetSchemaName = $targetSchema->getSchemaName();
			$selectQuery = new SelectQuery();
			$selectQuery->init();
			$relationAlias = sha1(random_bytes(64));
			$targetAlias = sha1(random_bytes(64));
			foreach ($targetSchema->getPropertyList() as $schemaProperty) {
				$selectQuery->select()->property($schemaProperty->getName(), $targetAlias);
			}
			$selectQuery->from()->source($relationSchema->getSchemaName(), $relationAlias)->source($targetSchemaName, $targetAlias)->where()->eq()->property($sourceLink->getSource()->getName(), $relationAlias)->parameter($crate->get($sourceLink->getTarget()->getName()))->and()->eq()->property($targetLink->getSource()->getName(), $relationAlias)->property($targetLink->getTarget()->getName(), $targetAlias);
			return $this->collection($targetSchemaName, $selectQuery, $crateTo ?: $targetSchemaName);
		}

		/**
		 * @inheritdoc
		 */
		public function collection(string $schema, IQuery $query = null, string $crate = null): ICollection {
			if ($query === null) {
				$query = new SelectQuery();
				$query->init();
				$query->select()->all()->from()->source($schema);
			}
			return new Collection($schema, $this, $this->crateFactory, $query, $crate);
		}

		/**
		 * @inheritdoc
		 * @throws EmptyResultException
		 */
		public function getLink(ICrate $crate, string $name): ICrate {
			$link = $crate->getSchema()->getLink($name);
			$selectQuery = new SelectQuery();
			$selectQuery->init();
			$targetSchemaName = $link->getTarget()->getSchema()->getSchemaName();
			$selectQuery->select()->all()->from()->source($targetSchemaName)->where()->eq()->property($link->getTarget()->getName())->parameter($crate->get($link->getSource()->getName()));
			$crate->link($link->getName(), $link = $this->load($targetSchemaName, $selectQuery, $this->crateFactory->hasCrate($targetSchemaName) ? $targetSchemaName : Crate::class));
			return $link;
		}

		/**
		 * @inheritdoc
		 * @throws EmptyResultException
		 */
		public function load(string $schema, IQuery $query, string $crate = null): ICrate {
			/** @noinspection LoopWhichDoesNotLoopInspection */
			foreach ($this->collection($schema, $query, $crate) as $item) {
				return $item;
			}
			throw new EmptyResultException(sprintf('Cannot retrieve any crate [%s] by the given query.', $schema));
		}
	}
