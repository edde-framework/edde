<?php
declare(strict_types=1);

namespace Edde\Service\Schema;

use Edde\Schema\ISchemaLoader;

trait SchemaLoader {
    /** @var ISchemaLoader */
    protected $schemaLoader;

    /**
     * @param ISchemaLoader $schemaLoader
     */
    public function injectSchemaLoader(ISchemaLoader $schemaLoader): void {
        $this->schemaLoader = $schemaLoader;
    }
}
