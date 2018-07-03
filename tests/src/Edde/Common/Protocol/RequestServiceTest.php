<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Application\IContext;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\Request\LazyRequestServiceTrait;
	use Edde\Common\Container\Factory\CascadeFactory;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\Protocol\Request\MissingResponseException;
	use Edde\Common\Protocol\Request\Request;
	use Edde\Common\Protocol\Request\UnhandledRequestException;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;
	use Edde\Test\ExecutableService;
	use Edde\Test\TestContext;

	require_once __DIR__ . '/../assets/assets.php';

	class RequestServiceTest extends TestCase {
		use LazyRequestServiceTrait;
		use LazyContainerTrait;

		public function testMissingResponse() {
			/** @var $response IElement */
			$response = $this->requestService->execute(new Request(sprintf('%s::noResponse', ExecutableService::class)));
			self::assertEquals('error', $response->getType());
			self::assertEquals(MissingResponseException::class, $response->getAttribute('exception'));
			self::assertEquals('Internal error; request [Edde\Test\ExecutableService::noResponse] got no answer (response).', $response->getAttribute('message'));
		}

		public function testUnhandlerRequest() {
			/** @var $response IElement */
			$response = $this->requestService->execute(new Request('unhandled'));
			self::assertEquals('error', $response->getType());
			self::assertEquals(UnhandledRequestException::class, $response->getAttribute('exception'));
			self::assertEquals('Unhandled request [unhandled].', $response->getAttribute('message'));
		}

		public function testContainerHandler() {
			/** @var $response IElement */
			$response = $this->requestService->execute((new Request(sprintf('%s::method', ExecutableService::class)))->data(['foo' => 'bababar']));
			self::assertEquals('response', $response->getType());
			self::assertEquals('bababar', $response->getMeta('got-this'));
		}

		public function testClassHandler() {
			/** @var $response IElement */
			$response = $this->requestService->execute((new Request('edde.test.executable-service/method'))->data(['foo' => 'barbar']));
			self::assertEquals('response', $response->getType());
			self::assertEquals('barbar', $response->getMeta('got-this'));
		}

		public function testContextClassHandler() {
			/** @var $response IElement */
			$response = $this->requestService->execute((new Request('test.executable-service/do-this'))->data(['foo' => 'barbar']));
			self::assertEquals('response', $response->getType());
			self::assertEquals('barbar', $response->getMeta('got-this'));
		}

		protected function setUp() {
			ContainerFactory::autowire($this, [
				IContext::class => TestContext::class,
				new ClassFactory(),
				new CascadeFactory(),
			]);
		}
	}
