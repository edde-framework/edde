<?php
	declare(strict_types=1);
	namespace App\Common\Upgrade;

		use App\Api\Upgrade\UpgradeSchema;
		use Edde\Api\Entity\ICollection;
		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Api\Upgrade\IUpgrade;
		use Edde\Common\Entity\Query\CreateSchemaQuery;
		use Edde\Common\Upgrade\AbstractUpgradeManager;

		class UpgradeManager extends AbstractUpgradeManager {
			use EntityManager;
			use SchemaManager;

			/**
			 * @inheritdoc
			 */
			public function getVersion(): ?string {
				try {
					return $this->getCurrentList()->getEntity()->get('version');
				} catch (UnknownTableException $exception) {
					/**
					 * when query fails, it's necessary to clear current transaction and start a new one
					 */
					$this->storage->rollback();
					$this->storage->start();
					$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(UpgradeSchema::class)));
					return null;
				} catch (EntityNotFoundException $exception) {
					return null;
				}
			}

			/**
			 * @inheritdoc
			 */
			public function getCurrentList(): ICollection {
				return $this->entityManager->collection(UpgradeSchema::class)->order('c.stamp', false);
			}

			/**
			 * @inheritdoc
			 */
			protected function onUpgrade(IUpgrade $upgrade): void {
				$this->entityManager->create(UpgradeSchema::class, [
					'stamp'   => new \DateTime(),
					'version' => $upgrade->getVersion(),
				])->save();
			}
		}
