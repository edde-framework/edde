<?php
	declare(strict_types=1);
	namespace Edde\Common\Upgrade;

	use Edde\Api\Storage\Exception\ExclusiveTransactionException;
	use Edde\Api\Storage\Exception\NoTransactionException;
	use Edde\Api\Storage\Inject\Storage;
	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Common\Object\Object;
	use Edde\Inject\Log\LogService;
	use Throwable;

	abstract class AbstractUpgrade extends Object implements IUpgrade {
		use Storage;
		use LogService;

		/**
		 * @inheritdoc
		 *
		 * @throws ExclusiveTransactionException
		 */
		public function onStart(): void {
			$this->storage->start();
		}

		/**
		 * @inheritdoc
		 *
		 * @throws NoTransactionException
		 */
		public function onSuccess(): void {
			$this->storage->commit();
		}

		/** @inheritdoc */
		public function onFail(Throwable $throwable): void {
			$this->logService->exception($throwable);
			$this->storage->rollback();
			throw $throwable;
		}
	}
