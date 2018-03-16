<?php
	declare(strict_types=1);
	namespace Edde\Common\Upgrade;

	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Exception\Storage\ExclusiveTransactionException;
	use Edde\Exception\Storage\NoTransactionException;
	use Edde\Inject\Log\LogService;
	use Edde\Inject\Storage\Storage;
	use Edde\Object;
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
