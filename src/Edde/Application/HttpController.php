<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Content\GeneratorContent;
	use Edde\Content\HtmlContent;
	use Edde\Content\JsonContent;
	use Edde\Content\NoContent;
	use Edde\Content\TextContent;
	use Edde\Http\EmptyBodyException;
	use Edde\Http\IResponse;
	use Edde\Http\Response;
	use Edde\Io\IFile;
	use Edde\Service\Http\RequestService;
	use Edde\Service\Schema\SchemaManager;

	/**
	 * Http control provides helpers for a http response style.
	 */
	trait HttpController {
		use RequestService;
		use SchemaManager;

		public function __call(string $name, $arguments) {
			$response = new Response();
			$response->setCode(IResponse::R400_BAD_REQUEST);
			$response->execute();
		}

		/**
		 * @param string $type
		 *
		 * @return mixed
		 *
		 * @throws EmptyBodyException
		 */
		protected function getContent(string $type = 'array') {
			return $this->requestService->getContent([$type]);
		}

		/**
		 * execute response with json based data
		 *
		 * @param mixed $content
		 * @param int   $code
		 *
		 * @return IResponse
		 */
		public function json($content, int $code = IResponse::R200_OK): IResponse {
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
		public function html($content, int $code = IResponse::R200_OK): IResponse {
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
		public function text($content, int $code = IResponse::R200_OK): IResponse {
			return $this->response(new Response(new TextContent($content)), $code);
		}

		/**
		 * return no-content response
		 */
		public function sendNoContent(): void {
			$this->response(
				new Response(new NoContent()),
				IResponse::R200_NO_CONTENT
			)->execute();
		}

		/**
		 * return empty created response
		 */
		public function sendCreated(): void {
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
		public function generator(callable $generator, string $type = 'text/plain', int $code = IResponse::R200_OK): IResponse {
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
		public function response(IResponse $response, int $code = IResponse::R200_OK) {
			$response->header('X-Powered-By', 'Edde Framework "' . self::$codename . '" ' . self::$framework);
			$response->header('Access-Control-Allow-Origin', '*');
			$response->header('Access-Control-Expose-Headers', '*');
			$response->setCode($code);
			return $response;
		}
	}
