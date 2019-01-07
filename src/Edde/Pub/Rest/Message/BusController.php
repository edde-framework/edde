<?php
	declare(strict_types=1);
	namespace Edde\Pub\Rest\Message;

	use Edde\Application\ApplicationException;
	use Edde\Controller\RestController;
	use Edde\Http\EmptyBodyException;
	use Edde\Http\IResponse;
	use Edde\Message\MessageException;
	use Edde\Service\Message\MessageBus;
	use Edde\Url\UrlException;

	class BusController extends RestController {
		use MessageBus;

		public function actionGet() {
			$this->jsonResponse(
				$this->messageBus->createPacket()->message($this->messageBus->createMessage('error', null, [
					'text' => 'Nope, GET is not the right method how to talk with me. So - please - *POST* me some messages.',
				]))->export(),
				IResponse::R400_BAD_REQUEST
			)->execute();
		}

		/**
		 * @throws ApplicationException
		 * @throws MessageException
		 * @throws UrlException
		 */
		public function actionPost() {
			try {
				$this->jsonResponse(
					$this->messageBus->packet($this->messageBus->importPacket($this->jsonRequest()))->export()
				)->execute();
			} catch (EmptyBodyException $exception) {
				$this->jsonResponse(
					$this->messageBus->createPacket()->export()
				)->execute();
			}
		}
	}
