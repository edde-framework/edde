<?php
declare(strict_types=1);

namespace Edde\Message;

use stdClass;

interface IPacket {
    /**
     * add a message which should be executed "on the other side"
     *
     * @param IMessage $message
     *
     * @return IPacket
     */
    public function message(IMessage $message): IPacket;

    /**
     * return an array of request messages
     *
     * @return IMessage[]
     */
    public function messages(): array;

    /**
     * export packet as an standard object
     *
     * @return stdClass
     */
    public function export(): stdClass;
}
