<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Service\Container\Container;
	use Edde\Service\Utils\StringUtils;
	use stdClass;
	use Throwable;
	use function is_string;
	use function sprintf;

	class MessageBus extends AbstractMessageHandler implements IMessageBus {
		use StringUtils;
		use Container;

		/** @inheritdoc */
		public function packet(IPacket $packet): IPacket {
			$response = $this->createPacket();
			foreach ($packet->messages() as $message) {
				try {
					$this->message($message, $response);
				} catch (Throwable $e) {
					$response->message($this->createMessage('error', 'common', [
						'message' => $message->export(),
						'text'    => $e->getMessage(),
					]));
				}
			}
			return $response;
		}

		/** @inheritdoc */
		public function resolve(IMessage $message): IMessageHandler {
			$service = $this->stringUtils->className(sprintf('%s.%sMessageHandler', $message->getNamespace(), $message->getType()));
			if ($this->container->canHandle($service) === false) {
				$common = $this->stringUtils->className(sprintf('Message.%sMessageHandler', $message->getType()));
				if ($this->container->canHandle($common) === false) {
					throw new MessageException(sprintf('Cannot resolve Message Handler for message [%s] uuid [%s] for namespace [%s]; please register a service [%s or %s] (IMessageHandler).', $message->getType(), $message->getUuid(), $message->getNamespace(), $service, $common));
				}
				$service = $common;
			}
			$instance = $this->container->create($service, [], __METHOD__);
			if ($instance instanceof IMessageHandler === false) {
				throw new MessageException(sprintf('Message handler service [%s] does not implement interface [%s].', $service, IMessageHandler::class));
			}
			return $instance;
		}

		/** @inheritdoc */
		public function createPacket(): IPacket {
			return new Packet($this->randomService->uuid());
		}

		/** @inheritdoc */
		public function message(IMessage $message, IPacket $packet): IMessageHandler {
			$this->resolve($message)->message($message, $packet);
			return $this;
		}

		/** @inheritdoc */
		public function import(stdClass $import): IPacket {
			$packet = $this->createPacket();
			foreach ($import->messages ?? [] as $item) {
				if (is_string($item->type ?? null) === false) {
					throw new MessageException('Missing message type or it is not string');
				}
				if (is_string($item->namespace ?? null) === false) {
					throw new MessageException('Missing message namespace or it is not string');
				}
				if (is_string($item->uuid ?? null) === false) {
					throw new MessageException('Missing message uuid or it is not string');
				}
				$packet->message(new Message($item->type, $item->namespace, $item->uuid, $item->attrs ?? null));
			}
			return $packet;
		}
	}
