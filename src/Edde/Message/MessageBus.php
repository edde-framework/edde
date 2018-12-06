<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Service\Container\Container;
	use Edde\Service\Utils\StringUtils;
	use function sprintf;

	class MessageBus extends AbstractMessageHandler implements IMessageBus {
		use StringUtils;
		use Container;

		/** @inheritdoc */
		public function packet(IPacket $packet): IPacket {
			$response = $this->createPacket();
			foreach ($packet->pushes() as $message) {
				$this->push($message, $response);
			}
			foreach ($packet->pulls() as $message) {
				$this->pull($message, $response);
			}
			return $response;
		}

		/** @inheritdoc */
		public function resolve(IMessage $message): IMessageHandler {
			$service = $this->stringUtils->className(sprintf('%s.%sMessageHandler', $message->getNamespace(), $message->getType()));
			if ($this->container->canHandle($service) === false) {
				throw new MessageException(sprintf('Cannot resolve Message Handler for message [%s] uuid [%s] for namespace [%s]; please register a service [%s] (IMessageHandler).', $message->getType(), $message->getUuid(), $message->getNamespace(), $service));
			}
			$instance = $this->container->create($service, [], __METHOD__);
			if ($instance instanceof IMessageHandler === false) {
				throw new MessageException(sprintf('Message handler service [%s] does not implement interface [%s].', $service, IMessageHandler::class));
			}
			return $instance;
		}

		/** @inheritdoc */
		public function createPacket(): IPacket {
			return new Packet(self::VERSION, $this->randomService->uuid());
		}

		/** @inheritdoc */
		public function push(IMessage $message, IPacket $packet): IMessageHandler {
			$this->resolve($message)->push($message, $packet);
			return $this;
		}

		/** @inheritdoc */
		public function pull(IMessage $message, IPacket $packet): IMessageHandler {
			$this->resolve($message)->pull($message, $packet);
			return $this;
		}
	}
