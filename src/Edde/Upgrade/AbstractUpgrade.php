<?php
declare(strict_types=1);

namespace Edde\Upgrade;

use Edde\Edde;
use Edde\Service\Log\LogService;
use Edde\Service\Storage\Storage;
use Throwable;

abstract class AbstractUpgrade extends Edde implements IUpgrade {
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
