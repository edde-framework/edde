<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Service\Container\Container;
	use Edde\Service\Utils\StringUtils;
	use stdClass;
	use Throwable;
	use function implode;
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
					$response->message($this->createMessage('error', 'common', [
						'message' => $message->export(),
						'text'    => $e->getMessage(),
					]));
				}
			}
			return $response;
		}

		/** @inheritdoc */
		public function resolve(IMessage $message): IMessageService {
			$resolve = [];
			if (($target = $message->getTarget()) && ($target = $this->stringUtils->className($message->getTarget()))) {
				$resolve[] = $target;
			}
			$resolve[] = sprintf('Message\\%sMessageService', $this->stringUtils->className($message->getType()));
			$resolve[] = sprintf('Message\\CommonMessageService');
			/** @var $service string */
			$service = null;
			foreach ($resolve as $name) {
				if ($this->container->canHandle($name)) {
					$service = $name;
					break;
				}
			}
			if ($service === null) {
				throw new MessageException(sprintf('Cannot resolve Message Service for message [%s] for target [%s]; please register one of [%s] (%s).', $message->getType(), $message->getTarget() ?: '- no target -', implode(', ', $resolve), IMessageService::class));
			}
			if (($instance = $this->container->create($service, [], __METHOD__)) instanceof IMessageService === false) {
				throw new MessageException(sprintf('Message service [%s] does not implement interface [%s].', $target, IMessageService::class));
			}
			return $instance;
		}

		/** @inheritdoc */
		public function execute(IMessage $message): IPacket {
			$this->message($message, $packet = $this->createPacket());
			return $packet;
		}

		/** @inheritdoc */
		public function createPacket(): IPacket {
			return new Packet();
		}

		/** @inheritdoc */
		public function message(IMessage $message, IPacket $packet): IMessageService {
			$this->resolve($message)->message($message, $packet);
			return $this;
		}

		/** @inheritdoc */
		public function importPacket(stdClass $import): IPacket {
			$packet = $this->createPacket();
			foreach ($import->messages ?? [] as $item) {
				$packet->message($this->importMessage($item));
			}
			return $packet;
		}
	}
