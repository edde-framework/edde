<?php
	namespace Edde\Ext\Control;

		use Edde\Api\Http\IResponse;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Content\TextContent;
		use Edde\Common\Http\Response;

		/**
		 * Provides helpful methods around implementing REST service.
		 */
		trait RestController {
			use HttpController;
			use StringUtils;

			public function __call(string $name, $arguments) {
				$response = new Response();
				$response->setCode(IResponse::R400_BAD_REQUEST);
				if ($match = $this->stringUtils->match($name, '~^action(?<method>[a-z]+)$~i', true)) {
					$response->setContent(new TextContent(sprintf('Requested method [%s] is not allowed.', strtoupper($match['method']))));
					$response->setCode(IResponse::R400_NOT_ALLOWED);
				}
				return $response->execute();
			}
		}
