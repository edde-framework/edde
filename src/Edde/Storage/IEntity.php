<?php
declare(strict_types=1);

namespace Edde\Storage;

use ArrayAccess;

interface IEntity extends ArrayAccess {
    /**
     * return schema name of this entity
     *
     * @return string
     */
    public function getSchema(): string;

    /**
     * put (merge) values in an entity
     *
     * @param array $put
     *
     * @return IEntity
     */
    public function put(array $put): IEntity;

    /**
     * return internal source
     *
     * @return array
     */
    public function toArray(): array;
}
