<?php
declare(strict_types=1);

namespace Edde\Xml;

/**
 * Implementation of xml handler.
 */
interface IXmlHandler {
    /**
     * @param string $text
     */
    public function onTextEvent(string $text): void;

    /**
     * @param string $docType
     */
    public function onDocTypeEvent(string $docType): void;

    /**
     * @param string $tag
     * @param array  $attributes
     */
    public function onOpenTagEvent(string $tag, array $attributes): void;

    /**
     * @param string $name
     */
    public function onCloseTagEvent(string $name): void;

    /**
     * @param string $tag
     * @param array  $attributes
     */
    public function onShortTagEvent(string $tag, array $attributes): void;

    /**
     * @param string $header
     */
    public function onHeaderEvent(string $header): void;
}
