<?php
	declare(strict_types=1);
	namespace Edde\Common\Bus;

	use Edde\Bus\AbstractListener;
	use Edde\Element\IElement;
	use Edde\Element\IResponse;
	use Edde\Element\Response;
	use Edde\Object;

	class CommonListener extends AbstractListener {
		/** @inheritdoc */
		public function getListeners(): iterable {
			yield 'foo' => [$this, 'eventFoo'];
			yield 'bar' => [$this, 'eventBar'];
		}

		public function eventFoo(IElement $event, IElement $response) {
			$response->setAttribute('foo-was-here', true);
		}

		public function eventBar(IElement $event, IElement $response) {
			$response->setAttribute('bar-was-here', true);
		}
	}

	class SomeService extends Object {
		public function requestedMethod(): IResponse {
			$response = new Response('foo');
			$response->setAttribute('yaay', true);
			return $response;
		}
	}
