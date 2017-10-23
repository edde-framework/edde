<?php
	namespace App\Common\Upgrade;

		use App\Api\Upgrade\UpgradeSchema;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\UnknownTableException;
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
					$query->table(UpgradeSchema::class, 'u')->all()->order()->desc('stamp')->asc('version');
					$entity = $this->storage->load(UpgradeSchema::class, $query);
					return $entity->get('version');
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
			protected function onUpgrade(IUpgrade $upgrade): void {
				$this->storage->save($this->entityManager->create(UpgradeSchema::class, [
					'stamp'   => microtime(true),
					'version' => $upgrade->getVersion(),
				]));
			}
		}
