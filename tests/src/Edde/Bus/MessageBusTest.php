<?php
	declare(strict_types=1);
	namespace Edde\Bus;

	use Edde\Common\Bus\CommonListener;
	use Edde\Common\Bus\SomeService;
	use Edde\Container\ContainerException;
	use Edde\Content\Content;
	use Edde\Converter\ConverterException;
	use Edde\Element\Element;
	use Edde\Element\Event;
	use Edde\Element\IElement;
	use Edde\Element\IError;
	use Edde\Element\IResponse;
	use Edde\Element\Request;
	use Edde\Inject\Bus\EventBus;
	use Edde\Inject\Bus\MessageBus;
	use Edde\Inject\Converter\ConverterManager;
	use Edde\Inject\Crypt\RandomService;
	use Edde\Inject\Validator\ValidatorManager;
	use Edde\TestCase;
	use Edde\Validator\ValidationException;
	use Edde\Validator\ValidatorException;

	class MessageBusTest extends TestCase {
		use EventBus;
		use MessageBus;
		use ValidatorManager;
		use ConverterManager;
		use RandomService;

		/**
		 * @throws ContainerException
		 * @throws \Edde\Validator\ValidationException
		 */
		public function testUnknownMessageType() {
			$response = $this->messageBus->execute(new Element('unsupported-message', 'uuid'));
			self::assertSame('error', $response->getType());
			self::assertSame('Cannot handle element [unsupported-message (Edde\Element\Element)] in any handler; element type is not supported.', $response->getAttribute('message'));
			self::assertSame(BusException::class, $response->getAttribute('class'));
		}

		/**
		 * @throws ContainerException
		 * @throws ValidationException
		 * @throws ValidatorException
		 */
		public function testEventBusInvalidEvent() {
			$response = $this->messageBus->execute($message = new Element('event', 'uuid'));
			self::assertFalse($this->validatorManager->getValidator('message-bus:type:event')->isValid($message), 'an event message should NOT be valid!');
			self::assertSame('error', $response->getType());
			self::assertSame('An event message [Edde\Element\Element] has missing "event" attribute!', $response->getAttribute('message'));
			self::assertSame(ValidationException::class, $response->getAttribute('class'));
		}

		/**
		 * @throws ContainerException
		 * @throws ValidationException
		 */
		public function testEventBus() {
			$this->eventBus->registerListener(new CommonListener());
			$response = $this->messageBus->execute(new Event('foo', 'uuid'));
			self::assertEquals('response', $response->getType());
			self::assertTrue($response->getAttribute('foo-was-here'), 'the value of event response does not contain expected attribute!');
		}

		/**
		 * @throws ContainerException
		 * @throws ValidationException
		 */
		public function testInvalidRequest() {
			$response = $this->messageBus->execute(new Element('request', 'uuid'));
			self::assertInstanceOf(IError::class, $response);
			self::assertSame([
				'message' => 'A request message [Edde\Element\Element] has missing "service" attribute!',
				'code'    => 0,
				'class'   => ValidationException::class,
			], $response->getAttributes());
		}

		/**
		 * @throws BusException
		 * @throws ValidationException
		 */
		public function testInvalidRequestValidation() {
			$this->expectException(ValidationException::class);
			$this->expectExceptionMessage('A request message [Edde\Element\Element] has missing "service" attribute!');
			$this->messageBus->validate(new Element('request', 'uuid'));
		}

		/**
		 * @throws ContainerException
		 * @throws ValidationException
		 */
		public function testRequest() {
			$response = $this->messageBus->execute(new Request(SomeService::class, 'requestedMethod', 'uuid'));
			self::assertInstanceOf(IResponse::class, $response);
			self::assertTrue($response->getAttribute('yaay'));
		}

		public function testExportImport() {
			$object = $this->messageBus->export($request = new Element('request', 'uuid', [
				'service'    => 'foo-service',
				'method'     => 'bar-method',
				'parameters' => ['do' => true, 'something' => 3.14],
			]));
			self::assertInstanceOf(\stdClass::class, $object);
			self::assertEquals($request, $this->messageBus->import($object));
		}

		/**
		 * @throws ConverterException
		 */
		public function testMessageImport() {
			$request = new Element('request', 'uuid', $attributes = [
				'service'    => 'foo-service',
				'method'     => 'bar-method',
				'parameters' => (object)['do' => true, 'something' => 3.14],
			]);
			$json = $this->converterManager->convert(new Content($request, IElement::class), ['application/json']);
			$content = $this->converterManager->convert($json, [IElement::class]);
			/** @var $element IElement */
			self::assertInstanceOf(IElement::class, $element = $content->getContent());
			self::assertSame(IElement::class, $content->getType());
			self::assertEquals($attributes, $element->getAttributes());
		}
	}
