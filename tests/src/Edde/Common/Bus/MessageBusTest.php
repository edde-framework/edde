<?php
	declare(strict_types=1);
	namespace Edde\Common\Bus;

	use Edde\Api\Bus\Exception\InvalidElementException;
	use Edde\Api\Bus\Exception\UnhandledElementException;
	use Edde\Api\Bus\IElement;
	use Edde\Api\Bus\IError;
	use Edde\Api\Bus\Inject\EventBus;
	use Edde\Api\Bus\Inject\MessageBus;
	use Edde\Api\Bus\Request\IResponse;
	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Converter\Exception\ConverterException;
	use Edde\Api\Converter\Inject\ConverterManager;
	use Edde\Api\Crypt\Inject\RandomService;
	use Edde\Api\Validator\Exception\UnknownValidatorException;
	use Edde\Api\Validator\Exception\ValidationException;
	use Edde\Api\Validator\Inject\ValidatorManager;
	use Edde\Common\Bus\Event\Event;
	use Edde\Common\Bus\Request\Request;
	use Edde\Common\Content\Content;
	use Edde\TestCase;

	class MessageBusTest extends TestCase {
		use EventBus;
		use MessageBus;
		use ValidatorManager;
		use ConverterManager;
		use RandomService;

		/**
		 * @throws InvalidElementException
		 * @throws ValidationException
		 * @throws ContainerException
		 */
		public function testUnknownMessageType() {
			$response = $this->messageBus->execute(new Element('unsupported-message', 'uuid'));
			self::assertSame('error', $response->getType());
			self::assertSame('Cannot handle element [unsupported-message (Edde\Common\Bus\Element)] in any handler; element type is not supported.', $response->getAttribute('message'));
			self::assertSame(UnhandledElementException::class, $response->getAttribute('class'));
		}

		/**
		 * @throws ContainerException
		 * @throws InvalidElementException
		 * @throws UnknownValidatorException
		 * @throws ValidationException
		 */
		public function testEventBusInvalidEvent() {
			$response = $this->messageBus->execute($message = new Element('event', 'uuid'));
			self::assertFalse($this->validatorManager->getValidator('message-bus:type:event')->isValid($message), 'an event message should NOT be valid!');
			self::assertSame('error', $response->getType());
			self::assertSame('An event message [Edde\Common\Bus\Element] has missing "event" attribute!', $response->getAttribute('message'));
			self::assertSame(ValidationException::class, $response->getAttribute('class'));
		}

		/**
		 * @throws ContainerException
		 * @throws InvalidElementException
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
		 * @throws InvalidElementException
		 * @throws ValidationException
		 */
		public function testInvalidRequest() {
			$response = $this->messageBus->execute(new Element('request', 'uuid'));
			self::assertInstanceOf(IError::class, $response);
			self::assertSame([
				'message' => 'A request message [Edde\Common\Bus\Element] has missing "service" attribute!',
				'code'    => 0,
				'class'   => ValidationException::class,
			], $response->getAttributes());
		}

		/**
		 * @throws InvalidElementException
		 * @throws ValidationException
		 */
		public function testInvalidRequestValidation() {
			$this->expectException(ValidationException::class);
			$this->expectExceptionMessage('A request message [Edde\Common\Bus\Element] has missing "service" attribute!');
			$this->messageBus->validate(new Element('request', 'uuid'));
		}

		/**
		 * @throws ContainerException
		 * @throws InvalidElementException
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
