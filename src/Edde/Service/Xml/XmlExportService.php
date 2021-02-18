<?php
declare(strict_types=1);

namespace Edde\Service\Xml;

use Edde\Xml\IXmlExportService;

trait XmlExportService {
    /** @var IXmlExportService */
    protected $xmlExportService;

    /**
     * @param IXmlExportService $xmlExportService
     */
    public function injectXmlExportService(IXmlExportService $xmlExportService): void {
        $this->xmlExportService = $xmlExportService;
    }
}
