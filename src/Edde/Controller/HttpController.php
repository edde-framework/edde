<?php
	declare(strict_types=1);
	namespace Edde\Controller;

	use Edde\Application\ApplicationException;
	use Edde\Content\GeneratorContent;
	use Edde\Content\HtmlContent;
	use Edde\Content\JsonContent;
	use Edde\Content\NoContent;
	use Edde\Content\TextContent;
	use Edde\File\IFile;
	use Edde\Http\EmptyBodyException;
	use Edde\Http\IResponse;
	use Edde\Http\Response;
	use Edde\Service\Http\RequestService;
	use stdClass;
	use function json_decode;

	/**
	 * Http control provides helpers for a http response style.
	 */
	class HttpController extends AbstractController {
		use RequestService;

		public function __call(string $name, $arguments) {
			$response = new Response();
			$response->setCode(IResponse::R400_BAD_REQUEST);
			$response->execute();
		}

		/**
		 * @param string $expected
		 *
		 * @return mixed
		 *
		 * @throws ApplicationException
		 * @throws EmptyBodyException
		 */
		protected function getContent(string $expected) {
			if (($content = $this->requestService->getContent())->getType() !== 'application/json') {
				throw new ApplicationException(sprintf('Content mismatch: expected [%s], got [%s]', $expected, $content->getType()));
			}
			return $content->getContent();
		}

		/**
		 * @return stdClass
		 *
		 * @throws ApplicationException
		 * @throws EmptyBodyException
		 */
		protected function jsonRequest(): stdClass {
			return json_decode($this->getContent('application/json'));
		}

		/**
		 * execute response with json based data
		 *
		 * @param mixed $content
		 * @param int   $code
		 *
		 * @return IResponse
		 */
		protected function jsonResponse($content, int $code = IResponse::R200_OK): IResponse {
			return $this->response(new Response(new JsonContent(json_encode($content))), $code);
		}

		/**
		 * execute response with html based data
		 *
		 * @param string|IFile $content
		 * @param int          $code
		 *
		 * @return IResponse
		 */
		protected function htmlResponse($content, int $code = IResponse::R200_OK): IResponse {
			return $this->response(new Response(new HtmlContent($content)), $code);
		}

		/**
		 * execute response with simple text content
		 *
		 * @param string|IFile $content
		 * @param int          $code
		 *
		 * @return IResponse
		 */
		protected function textResponse($content, int $code = IResponse::R200_OK): IResponse {
			return $this->response(new Response(new TextContent($content)), $code);
		}

		/**
		 * return no-content response
		 */
		protected function sendNoContent(): void {
			$this->response(
				new Response(new NoContent()),
				IResponse::R200_NO_CONTENT
			)->execute();
		}

		/**
		 * return empty created response
		 */
		protected function sendCreated(): void {
			$this->response(
				new Response(new NoContent()),
				IResponse::R200_OK_CREATED
			)->execute();
		}

		/**
		 * @param callable $generator
		 * @param string   $type
		 * @param int      $code
		 *
		 * @return IResponse
		 */
		protected function generatorResponse(callable $generator, string $type = 'text/plain', int $code = IResponse::R200_OK): IResponse {
			return $this->response(
				new Response(new GeneratorContent($generator, $type)),
				$code
			);
		}

		/**
		 * just prepare response
		 *
		 * @param IResponse $response
		 * @param int       $code
		 *
		 * @return IResponse
		 */
		protected function response(IResponse $response, int $code = IResponse::R200_OK) {
			$response->header('X-Powered-By', 'Edde Framework "' . self::$codename . '" ' . self::$framework);
			$response->header('Access-Control-Allow-Origin', '*');
			$response->header('Access-Control-Expose-Headers', '*');
			$response->setCode($code);
			return $response;
		}
	}
