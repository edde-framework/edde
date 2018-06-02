<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\IEntity;
	use Edde\Config\ISection;
	use Edde\Filter\FilterException;
	use Edde\Query\IQuery;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\StorageFilterService;
	use Edde\Service\Utils\StringUtils;
	use Edde\Service\Validator\ValidatorManager;
	use Edde\Validator\ValidatorException;
	use stdClass;
	use function sprintf;

	abstract class AbstractStorage extends AbstractTransaction implements IStorage {
		use ConfigService;
		use SchemaManager;
		use StringUtils;
		use StorageFilterService;
		use FilterManager;
		use ValidatorManager;
		/** @var string */
		protected $config;
		/** @var ISection */
		protected $section;
		/** @var ICompiler */
		protected $compiler;

		/**
		 * @param string $config
		 */
		public function __construct(string $config) {
			$this->config = $config;
		}

		/** @inheritdoc */
		public function saves(iterable $entities): IStorage {
			$this->transaction(function () use ($entities) {
				foreach ($entities as $entity) {
					$this->save($entity);
				}
			});
			return $this;
		}

		/** @inheritdoc */
		public function attach(IEntity $source, IEntity $target, string $relation): IEntity {
			$relationEntity = new Entity($relationSchema = $this->schemaManager->getSchema($relation));
			$relationSchema->checkRelation(
				$source->getSchema(),
				$target->getSchema()
			);
			$this->save($source);
			$this->save($target);
			$relationEntity->set(
				$relationSchema->getSource()->getName(),
				$source->getPrimary()->get()
			);
			$relationEntity->set(
				$relationSchema->getTarget()->getName(),
				$target->getPrimary()->get()
			);
			return $relationEntity;
		}

		/** @inheritdoc */
		public function link(IEntity $source, IEntity $target, string $relation): IEntity {
			$this->unlink($source, $target, $relation);
			return $this->attach($source, $target, $relation);
		}

		/** @inheritdoc */
		public function count(IQuery $query): array {
			try {
				foreach ($this->fetch($this->compiler->compile($query->count(true))) as $row) {
					return $row;
				}
				throw new StorageException(sprintf('Cannot get counts from a query.'));
			} finally {
				$query->count(false);
			}
		}

		/**
		 * @param array    $row
		 * @param string[] $selects
		 *
		 * @return IRow
		 *
		 * @throws FilterException
		 * @throws ValidatorException
		 */
		protected function row(array $row, array $selects): IRow {
			$items = [];
			foreach ($row as $k => $v) {
				[$alias, $property] = explode('.', $k, 2);
				$items[$alias] = $items[$alias] ?? new stdClass();
				$items[$alias]->$property = $v;
			}
			foreach ($items as $alias => $item) {
				$items[$alias] = $this->storageFilterService->output($selects[$alias], $item);
			}
			return new Row($items);
		}

		/** @inheritdoc */
		protected function handleSetup(): void {
			parent::handleSetup();
			$this->section = $this->configService->require($this->config);
			$this->compiler = $this->createCompiler();
		}
	}
