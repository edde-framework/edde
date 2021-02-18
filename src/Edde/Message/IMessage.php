<?php
declare(strict_types=1);

namespace Edde\Message;

use ArrayAccess;
use stdClass;

/**
 * Basic element of the whole concept of Message Bus; a message
 * is an individual piece to be processed on the line.
 */
interface IMessage extends ArrayAccess {
    /**
     * return type of a message
     *
     * @return string
     */
    public function getType(): string;

    /**
     * return message optional target (used for routing if necessary)
     *
     * @return string
     */
    public function getTarget(): ?string;

    /**
     * @return array
     */
    public function getAttrs(): ?array;

    /**
     * export message as an standard object (to be serialized)
     *
     * @return stdClass
     */
    public function export(): stdClass;
}
