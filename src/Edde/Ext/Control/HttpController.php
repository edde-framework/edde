<?php
	namespace Edde\Ext\Control;

		use Edde\Api\File\IFile;
		use Edde\Api\Http\Inject\RequestService;
		use Edde\Api\Http\IResponse;
		use Edde\Common\Content\HtmlContent;
		use Edde\Common\Content\JsonContent;
		use Edde\Common\Http\Response;

		/**
		 * Http control provides helpers for a http response style.
		 */
		trait HttpController {
			use RequestService;

			public function __call(string $name, $arguments) {
				$response = new Response();
				$response->setCode(IResponse::R400_BAD_REQUEST);
				$response->execute();
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
				$response = new Response(new JsonContent(json_encode($content)));
				$response->header('X-Powered-By', 'Edde Framework');
				$response->setCode($code);
				return $response->execute();
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
				$response = new Response(new HtmlContent($content));
				$response->header('X-Powered-By', 'Edde Framework');
				$response->setCode($code);
				return $response->execute();
			}
		}
