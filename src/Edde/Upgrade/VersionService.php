<?php
declare(strict_types=1);

namespace Edde\Upgrade;

use Edde\Service\Storage\Storage;
use Edde\Storage\Entity;
use Edde\Storage\UnknownTableException;
use Generator;
use Throwable;

class VersionService extends AbstractVersionService {
    use Storage;

    /** @inheritdoc */
    public function getVersion(): ?string {
        try {
            try {
                foreach ($this->storage->value($this->storage->query('SELECT version FROM u:schema ORDER BY stamp DESC', ['u' => UpgradeSchema::class])) as $version) {
                    return $version;
                }
                return null;
            } catch (UnknownTableException $exception) {
                $this->storage->create(UpgradeSchema::class);
                return null;
            }
        } catch (Throwable $exception) {
            throw new UpgradeException(sprintf('Cannot retrieve current version: %s', $exception->getMessage()), 0, $exception);
        }
    }

    /** @inheritdoc */
    public function update(string $version): IVersionService {
        $this->storage->insert(new Entity(UpgradeSchema::class, [
            'version' => $version,
        ]));
        return $this;
    }

    /** @inheritdoc */
    public function getCollection(): Generator {
        return $this->storage->schema(UpgradeSchema::class, $this->storage->query('SELECT * FROM u:schema ORDER BY stamp DESC', ['u' => UpgradeSchema::class]));
    }
}
