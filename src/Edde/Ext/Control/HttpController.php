<?php
	namespace Edde\Ext\Control;

		use Edde\Api\Http\IResponse;
		use Edde\Common\Content\JsonContent;
		use Edde\Common\Http\Response;

		/**
		 * Http control provides helpers for a http response style.
		 */
		trait HttpController {
			public function __call(string $name, $arguments) {
				$response = new Response();
				$response->setCode(IResponse::R400_BAD_REQUEST);
				return $response->execute();
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
				return (new Response(new JsonContent(json_encode($content))))->setCode($code)->execute();
			}
		}
