<?php
declare(strict_types=1);

namespace Edde\Message;

use Edde\Edde;
use Edde\Service\Utils\StringUtils;
use ReflectionClass;
use ReflectionMethod;
use stdClass;

abstract class AbstractMessageService extends Edde implements IMessageService {
    use StringUtils;

    /** @var ReflectionMethod[] */
    protected $routes = [];

    public function init() {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if ($match = $this->stringUtils->match($reflectionMethod->getName(), '~on(?<type>.*?)Message~', true)) {
                $this->routes[$this->stringUtils->recamel($match['type'])] = $reflectionMethod;
            }
        }
    }

    /** @inheritdoc */
    public function message(IMessage $message, IPacket $packet): IMessageService {
        if (isset($this->routes[$message->getType()]) === false) {
            throw new MessageException(sprintf('Cannot handle message [%s] in [%s]. Please implement %s::on%sMessage($message, $packet) method.', $message->getType(), static::class, static::class, $this->stringUtils->toCamelCase($message->getType())));
        }
        $this->routes[$message->getType()]->invoke($this, $message, $packet);
        return $this;
    }

    /** @inheritdoc */
    public function createMessage(string $type, string $target = null, array $attrs = null): IMessage {
        return new Message($type, $target, $attrs);
    }

    /** @inheritdoc */
    public function importMessage(stdClass $stdClass): IMessage {
        if (is_string($stdClass->type ?? null) === false) {
            throw new MessageException('Missing message type or it is not string');
        }
        return new Message($stdClass->type, $stdClass->target ?? null, ((array)$stdClass->attrs) ?? null);
    }

    protected function reply(IMessage $message, array $attrs = null): IMessage {
        return $this->createMessage($message->getType(), $message->getTarget(), $attrs);
    }
}
