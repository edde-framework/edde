<?php
declare(strict_types=1);

namespace Edde\Http;

use Edde\Content\IContent;

class Response extends AbstractHttp implements IResponse {
    /** @var int */
    protected $code;

    public function __construct(IContent $content = null) {
        parent::__construct(new Headers());
        $this->content = $content;
        $this->code = self::R200_OK;
    }

    /** @inheritdoc */
    public function setCode(int $code): IResponse {
        $this->code = $code;
        return $this;
    }

    /** @inheritdoc */
    public function getCode(): int {
        return $this->code;
    }

    /** @inheritdoc */
    public function execute(): IResponse {
        http_response_code($this->code);
        if ($this->content && $this->headers->has('Content-Type') === false) {
            $this->headers->add('Content-Type', $this->content->getType());
        }
        foreach ($this->headers as $name => $header) {
            header("$name: $header", false);
        }
        foreach ($this->content ?: [] as $chunk) {
            echo $chunk;
        }
        return $this;
    }
}
