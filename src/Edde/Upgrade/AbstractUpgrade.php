<?php
	declare(strict_types=1);
	namespace Edde\Upgrade;

	use Edde\Object;
	use Edde\Service\Connection\Connection;
	use Edde\Service\Log\LogService;
	use Throwable;

	abstract class AbstractUpgrade extends Object implements IUpgrade {
		use Connection;
		use LogService;

		/** @inheritdoc */
		public function onStart(): void {
			$this->connection->start();
		}

		/** @inheritdoc */
		public function onSuccess(): void {
			$this->connection->commit();
		}

		/** @inheritdoc */
		public function onFail(Throwable $throwable): void {
			$this->logService->exception($throwable);
			$this->connection->rollback();
			throw $throwable;
		}
	}
