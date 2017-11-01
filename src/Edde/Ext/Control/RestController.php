<?php
	declare(strict_types=1);
	namespace Edde\Ext\Control;

		use Edde\Api\Http\Exception\NoHttpException;
		use Edde\Api\Http\IResponse;
		use Edde\Api\Utils\Inject\StringUtils;
		use Edde\Common\Content\JsonContent;
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
					$response->setContent(new JsonContent(sprintf('Requested method [%s] is not allowed.', strtoupper($match['method']))));
					$response->setCode(IResponse::R400_NOT_ALLOWED);
					$response->header('Allow', implode(', ', $this->getAllowedList()));
				}
				return $response->execute();
			}

			/**
			 * @return string[]
			 */
			protected function getAllowedList(): array {
				$allowedList = [];
				$reflectionClass = new \ReflectionClass($this);
				foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
					if (strpos($name = $reflectionMethod->getName(), 'action') === 0 && strlen($name) > 6) {
						$allowedList[] = strtoupper(substr($name, 6));
					}
				}
				return $allowedList;
			}

			/**
			 * @throws NoHttpException
			 */
			public function actionOptions() {
				(new Response())->headers([
					'Access-Control-Allow-Methods' => implode(', ', $this->getAllowedList()),
					'Access-Control-Allow-Origin'  => '*',
					'Access-Control-Allow-Headers' => $this->requestService->getHeaders()->get('Access-Control-Request-Headers', '*'),
				])->execute();
			}
		}
