<?php
	declare(strict_types=1);
	namespace Edde\Common\Upgrade;

	use Edde\Api\Log\Inject\LogService;
	use Edde\Api\Storage\Inject\Storage;
	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Common\Object\Object;

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
		public function onFail(\Throwable $throwable): void {
			$this->logService->exception($throwable);
			$this->storage->rollback();
			throw $throwable;
		}
	}
