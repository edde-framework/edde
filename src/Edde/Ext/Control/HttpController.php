<?php
	declare(strict_types=1);
	namespace Edde\Ext\Control;

	use Edde\Api\File\IFile;
	use Edde\Api\Http\IResponse;
	use Edde\Api\Validator\Exception\UnknownValidatorException;
	use Edde\Api\Validator\Exception\ValidationException;
	use Edde\Common\Content\GeneratorContent;
	use Edde\Common\Content\HtmlContent;
	use Edde\Common\Content\JsonContent;
	use Edde\Common\Content\TextContent;
	use Edde\Common\Http\Response;
	use Edde\Exception\Http\EmptyBodyException;
	use Edde\Exception\Schema\UnknownPropertyException;
	use Edde\Exception\Schema\UnknownSchemaException;
	use Edde\Inject\Http\RequestService;
	use Edde\Inject\Schema\SchemaManager;

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
		 * validate http input by the given schema; internally current request body
		 * is used
		 *
		 * @param string $schema
		 *
		 * @throws EmptyBodyException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
		 * @throws UnknownPropertyException
		 * @throws UnknownValidatorException
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
