<?php
	declare(strict_types=1);
	namespace Edde\Upgrade;

	use Edde\Object;
	use Edde\Service\Log\LogService;
	use Edde\Service\Storage\Storage;
	use Throwable;

	abstract class AbstractUpgrade extends Object implements IUpgrade {
		use Storage;
		use LogService;

		/** @inheritdoc */
		public function onStart(): void {
			$this->storage->start();
		}

		/** @inheritdoc */
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
