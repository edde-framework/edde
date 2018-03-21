<?php
	declare(strict_types=1);
	namespace Edde\Control;

	use Edde\Content\Content;
	use Edde\Content\GeneratorContent;
	use Edde\Content\HtmlContent;
	use Edde\Content\JsonContent;
	use Edde\Content\TextContent;
	use Edde\File\IFile;
	use Edde\Http\EmptyBodyException;
	use Edde\Http\IResponse;
	use Edde\Http\Response;
	use Edde\Schema\SchemaException;
	use Edde\Schema\SchemaValidationException;
	use Edde\Service\Http\RequestService;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Validator\ValidatorException;

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
			return $this->requestService->getContent($type);
		}

		/**
		 * validate http input by the given schema; internally current request body
		 * is used
		 *
		 * @param string $schema
		 *
		 * @throws EmptyBodyException
		 * @throws SchemaException
		 * @throws SchemaValidationException
		 * @throws ValidatorException
		 */
		protected function validate(string $schema) {
			$this->schemaManager->check($schema, $this->requestService->getContent('array'));
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
		 *
		 * @return IResponse
		 */
		public function sendNoContent(): IResponse {
			return $this->response(new Response(new Content('', 'text/plain')), IResponse::R200_NO_CONTENT);
		}

		/**
		 * return empty created response
		 *
		 * @return IResponse
		 */
		public function sendCreated(): IResponse {
			return $this->response(new Response(new Content('', 'text/plain')), IResponse::R200_OK_CREATED);
		}

		/**
		 * @param callable $generator
		 * @param string   $type
		 * @param int      $code
		 *
		 * @return IResponse
		 */
		public function generator(callable $generator, string $type = 'text/plain', int $code = IResponse::R200_OK): IResponse {
			return $this->response(new Response(new GeneratorContent($generator, $type)), $code);
		}

		public function response(IResponse $response, int $code = IResponse::R200_OK) {
			$response->header('X-Powered-By', 'Edde Framework');
			$response->header('Access-Control-Allow-Origin', '*');
			$response->setCode($code);
			return $response->execute();
		}
	}
