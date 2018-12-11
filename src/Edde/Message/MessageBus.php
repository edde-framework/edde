<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Service\Container\Container;
	use Edde\Service\Utils\StringUtils;
	use stdClass;
	use Throwable;
	use function is_string;
	use function sprintf;

	class MessageBus extends AbstractMessageService implements IMessageBus {
		use StringUtils;
		use Container;

		/** @inheritdoc */
		public function packet(IPacket $packet): IPacket {
			$response = $this->createPacket();
			foreach ($packet->messages() as $message) {
				try {
					$this->message($message, $response);
				} catch (Throwable $e) {
					$response->message($this->createMessage('common', 'error', [
						'message' => $message->export(),
						'text'    => $e->getMessage(),
					]));
				}
			}
			return $response;
		}

		/** @inheritdoc */
		public function resolve(IMessage $message): IMessageService {
			$service = $this->stringUtils->className($message->getService());
			if ($this->container->canHandle($service) === false) {
				throw new MessageException(sprintf('Cannot resolve Message Handler for message [%s] uuid [%s] for namespace [%s]; please register a service [%s] (%s).', $message->getType(), $message->getUuid(), $message->getService(), $service, IMessageService::class));
			}
			if (($instance = $this->container->create($service, [], __METHOD__)) instanceof IMessageService === false) {
				throw new MessageException(sprintf('Message handler service [%s] does not implement interface [%s].', $service, IMessageService::class));
			}
			return $instance;
		}

		/** @inheritdoc */
		public function createPacket(): IPacket {
			return new Packet($this->randomService->uuid());
		}

		/** @inheritdoc */
		public function message(IMessage $message, IPacket $packet): IMessageService {
			$this->resolve($message)->message($message, $packet);
			return $this;
		}

		/** @inheritdoc */
		public function import(stdClass $import): IPacket {
			$packet = $this->createPacket();
			foreach ($import->messages ?? [] as $item) {
				if (is_string($item->service ?? null) === false) {
					throw new MessageException('Missing message service or it is not string');
				}
				if (is_string($item->type ?? null) === false) {
					throw new MessageException('Missing message type or it is not string');
				}
				if (is_string($item->uuid ?? null) === false) {
					throw new MessageException('Missing message uuid or it is not string');
				}
				$packet->message(new Message($item->service, $item->type, $item->uuid, ((array)$item->attrs) ?? null));
			}
			return $packet;
		}
	}
