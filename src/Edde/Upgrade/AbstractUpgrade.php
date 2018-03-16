<?php
	declare(strict_types=1);
	namespace Edde\Upgrade;

	use Edde\Inject\Log\LogService;
	use Edde\Inject\Storage\Storage;
	use Edde\Object;
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
