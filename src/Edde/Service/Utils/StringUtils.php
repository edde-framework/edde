<?php
declare(strict_types=1);

namespace Edde\Service\Utils;

use Edde\Utils\IStringUtils;

trait StringUtils {
    /** @var IStringUtils */
    protected $stringUtils;

    /**
     * @param IStringUtils $stringUtils
     */
    public function injectStringUtils(IStringUtils $stringUtils) {
        $this->stringUtils = $stringUtils;
    }
}
