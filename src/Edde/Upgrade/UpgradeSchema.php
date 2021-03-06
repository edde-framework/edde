<?php
declare(strict_types=1);

namespace Edde\Upgrade;

use DateTime;
use Edde\Schema\UuidSchema;

interface UpgradeSchema extends UuidSchema {
    const alias = true;

    public function version($unique): string;

    public function stamp($generator = 'stamp'): DateTime;
}
