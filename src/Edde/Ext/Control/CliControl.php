<?php
	namespace Edde\Ext\Control;

		use Edde\Api\Response\Inject\ResponseService;
		use Edde\Api\Response\IResponse;
		use Edde\Common\Content\CallableContent;
		use Edde\Common\Response\Response;

		/**
		 * Control used for a command line content rendering.
		 */
		trait CliControl {
			use ResponseService;

			/**
			 * execute a cli response using generator
			 *
			 * @param callable $generator
			 * @param int      $code
			 *
			 * @return IResponse
			 */
			public function send(callable $generator, int $code = 0): IResponse {
				$this->responseService->execute($response = new Response(new CallableContent($generator), $code));
				return $response;
			}
		}
