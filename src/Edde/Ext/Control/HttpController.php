<?php
	namespace Edde\Ext\Control;

		use Edde\Api\Http\IResponse;
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
		}
