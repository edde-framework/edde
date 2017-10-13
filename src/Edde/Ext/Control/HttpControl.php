<?php
	namespace Edde\Ext\Control;

		use Edde\Api\Http\Inject\ResponseService;
		use Edde\Api\Http\IResponse;
		use Edde\Common\Content\CallableContent;
		use Edde\Common\Http\ContentType;
		use Edde\Common\Http\Cookies;
		use Edde\Common\Http\Headers;
		use Edde\Common\Http\Response;

		/**
		 * Http control provides helpers for a http response style.
		 */
		trait HttpControl {
			use ResponseService;

			/**
			 * prepare a http response object; this
			 *
			 * @param callable $generator
			 *
			 * @return IResponse
			 */
			public function http(callable $generator): IResponse {
				return new Response(new CallableContent($generator), new Headers(), new Cookies());
			}

			/**
			 * directly send a response without messing with http headers
			 *
			 * @param callable $generator
			 * @param string   $mime
			 * @param int      $code
			 *
			 * @return IResponse
			 */
			public function send(callable $generator, string $mime = 'text/plain', int $code = IResponse::R200_OK): IResponse {
				$response = $this->http($generator);
				$response->setCode($code);
				$response->setContentType(new ContentType($mime, ['charset' => 'utf-8']));
				$this->responseService->execute($response);
				return $response;
			}
		}
