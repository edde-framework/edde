<?php
	namespace App\Common\Upgrade;

		use App\Api\Upgrade\UpgradeSchema;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Api\Storage\ICollection;
		use Edde\Api\Storage\Inject\EntityManager;
		use Edde\Api\Upgrade\IUpgrade;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Common\Query\SelectQuery;
		use Edde\Common\Upgrade\AbstractUpgradeManager;

		class UpgradeManager extends AbstractUpgradeManager {
			use EntityManager;
			use SchemaManager;

			/**
			 * @inheritdoc
			 */
			public function getVersion(): ?string {
				try {
					$query = new SelectQuery();
					$query->setDescription('current version select');
					$query->table(UpgradeSchema::class)->all()->order()->desc('stamp');
					return $this->storage->load(UpgradeSchema::class, $query)->get('version');
				} catch (UnknownTableException $exception) {
					$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema(UpgradeSchema::class)));
					return null;
				} catch (EntityNotFoundException  $exception) {
					return null;
				}
			}

			/**
			 * @inheritdoc
			 */
			public function getCurrentList(): ICollection {
				return $this->storage->collection(UpgradeSchema::class, (new SelectQuery())->table(UpgradeSchema::class)->all()->order()->desc('stamp')->query());
			}

			/**
			 * @inheritdoc
			 */
			protected function onUpgrade(IUpgrade $upgrade): void {
				$this->storage->insert($this->entityManager->create(UpgradeSchema::class, [
					'stamp'   => microtime(true),
					'version' => $upgrade->getVersion(),
				]));
			}
		}
