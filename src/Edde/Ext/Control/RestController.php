<?php
	declare(strict_types=1);
	namespace Edde\Ext\Control;

	use Edde\Api\Http\Exception\NoHttpException;
	use Edde\Api\Http\IResponse;
	use Edde\Api\Utils\Inject\StringUtils;
	use Edde\Api\Validator\Exception\BatchValidationException;
	use Edde\Api\Validator\Exception\ValidatorException;
	use Edde\Common\Content\JsonContent;
	use Edde\Common\Http\Response;
	use ReflectionClass;
	use ReflectionException;

	/**
	 * Provides helpful methods around implementing REST service.
	 */
	trait RestController {
		use HttpController;
		use StringUtils;

		/** @inheritdoc */
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
		 * wrap callback into try-catch with standard json responses
		 *
		 * @param callable $exec exec should return array to be jsoned
		 * @param string   $schema
		 *
		 * @throws \Throwable
		 */
		protected function rest(callable $exec, string $schema) {
			try {
				$this->validate($schema);
				$this->json($exec($this->requestService->getContent('array')), IResponse::R200_OK);
			} catch (BatchValidationException $exception) {
				$this->json([
					'error'       => $exception->getMessage(),
					'code'        => $exception->getCode(),
					'exception'   => get_class($exception),
					'validations' => $exception->getValidations(),
				], IResponse::R400_BAD_REQUEST);
			} catch (ValidatorException $exception) {
				$this->json([
					'error'     => $exception->getMessage(),
					'code'      => $exception->getCode(),
					'exception' => get_class($exception),
				], IResponse::R400_BAD_REQUEST);
			} catch (\Throwable $exception) {
				$this->json([
					'error'     => $exception->getMessage(),
					'code'      => $exception->getCode(),
					'exception' => get_class($exception),
				], IResponse::R500_SERVER_ERROR);
				throw $exception;
			}
		}

		/**
		 * @return string[]
		 * @throws ReflectionException
		 */
		protected function getAllowedList(): array {
			$allowedList = [];
			$reflectionClass = new ReflectionClass($this);
			foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
				if (strpos($name = $reflectionMethod->getName(), 'action') === 0 && strlen($name) > 6) {
					$allowedList[] = strtoupper(substr($name, 6));
				}
			}
			return $allowedList;
		}

		/**
		 * @throws NoHttpException
		 * @throws ReflectionException
		 */
		public function actionOptions() {
			$response = new Response();
			$response->headers([
				'Access-Control-Allow-Methods' => implode(', ', $this->getAllowedList()),
				'Access-Control-Allow-Origin'  => '*',
				'Access-Control-Allow-Headers' => $this->requestService->getHeaders()->get('Access-Control-Request-Headers', '*'),
			]);
			$response->execute();
		}
	}
