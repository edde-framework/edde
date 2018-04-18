<?php
	declare(strict_types=1);
	namespace Edde\Upgrade;

	use Edde\Collection\ICollection;
	use Edde\Entity\EntityNotFoundException;
	use Edde\Service\Collection\CollectionManager;
	use Edde\Service\Entity\EntityManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\UnknownTableException;
	use Throwable;

	class UpgradeManager extends AbstractUpgradeManager {
		use EntityManager;
		use Storage;
		use SchemaManager;
		use CollectionManager;

		/** @inheritdoc */
		public function getVersion(): ?string {
			try {
				try {
					return null;
//					return $this->getCurrentCollection()->getEntity('u')->get('version');
				} catch (UnknownTableException $exception) {
					$this->storage->create($this->schemaManager->load(UpgradeSchema::class));
					return null;
				} catch (EntityNotFoundException $exception) {
					return null;
				}
			} catch (Throwable $exception) {
				throw new UpgradeException(sprintf('Cannot retrieve current version: %s', $exception->getMessage()), 0, $exception);
			}
		}

		/** @inheritdoc */
		public function getCurrentCollection(): ICollection {
//			return $this->entityManager->collection('u', UpgradeSchema::class)->order('u.stamp', false);
		}

		/** @inheritdoc */
		protected function onUpgrade(IUpgrade $upgrade): void {
			$this->entityManager->save(UpgradeSchema::class, [
				'version' => $upgrade->getVersion(),
			]);
		}
	}
