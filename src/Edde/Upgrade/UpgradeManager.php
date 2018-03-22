<?php
	declare(strict_types=1);
	namespace Edde\Upgrade;

	use Edde\Collection\ICollection;
	use Edde\Connection\UnknownTableException;
	use Edde\Entity\EntityNotFoundException;
	use Edde\Query\CreateSchemaQuery;
	use Edde\Service\Entity\EntityManager;
	use Edde\Service\Schema\SchemaManager;
	use Throwable;

	class UpgradeManager extends AbstractUpgradeManager {
		use EntityManager;
		use SchemaManager;

		/** @inheritdoc */
		public function getVersion(): ?string {
			try {
				try {
					return $this->getCurrentCollection()->getEntity('u')->get('version');
				} catch (UnknownTableException $exception) {
					$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(UpgradeSchema::class)));
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
			return $this->entityManager->collection('u', UpgradeSchema::class)->order('u.stamp', false);
		}

		/** @inheritdoc */
		protected function onUpgrade(IUpgrade $upgrade): void {
			$this->entityManager->save(UpgradeSchema::class, [
				'version' => $upgrade->getVersion(),
			]);
		}
	}
