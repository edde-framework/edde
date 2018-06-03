<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Collection\Entity;
	use Edde\Collection\EntityNotFoundException;
	use Edde\Collection\IEntity;
	use Edde\Config\ISection;
	use Edde\Query\IQuery;
	use Edde\Query\Query;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\StorageFilterService;
	use Edde\Service\Utils\StringUtils;
	use Throwable;
	use function sprintf;

	abstract class AbstractStorage extends AbstractTransaction implements IStorage {
		use ConfigService;
		use SchemaManager;
		use StringUtils;
		use StorageFilterService;
		/** @var string */
		protected $config;
		/** @var ISection */
		protected $section;
		/** @var ICompiler */
		protected $compiler;
		/** @var IQuery[] */
		protected $loads = [];

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
		public function load(string $schema, string $id): IEntity {
			try {
				if (isset($this->loads[$schema]) === false) {
					$chema = $this->schemaManager->getSchema($schema);
					$this->loads[$schema] = $query = new Query();
					$query->select($chema);
					$wheres = $query->wheres();
					$wheres->where('primary')->equalTo($schema, $chema->getPrimary()->getName());
					$wheres->chains()->chain()->where('primary');
				}
				foreach ($this->query($this->loads[$schema], ['primary' => $id]) as $record) {
					return $record->getEntity($schema);
				}
				throw new EntityNotFoundException(sprintf('Cannot load any entity [%s] with id [%s].', $schema, $id));
			} catch (EntityNotFoundException $exception) {
				throw $exception;
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->exception($exception);
			}
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
		public function count(IQuery $query): IRecord {
			try {
				foreach ($this->fetch($this->compiler->compile($query->count(true))) as $items) {
					return new Record($query, $items);
				}
				throw new StorageException(sprintf('Cannot get counts from a query.'));
			} finally {
				$query->count(false);
			}
		}

		/**
		 * @param Throwable $throwable
		 *
		 * @return Throwable
		 */
		protected function exception(Throwable $throwable): Throwable {
			return $throwable;
		}

		/** @inheritdoc */
		protected function handleSetup(): void {
			parent::handleSetup();
			$this->section = $this->configService->require($this->config);
			$this->compiler = $this->createCompiler();
		}
	}
