<?php
	declare(strict_types=1);
	namespace Edde\Common\Application;

	use Edde\Api\Application\Exception\AbortException;
	use Edde\Api\Application\IApplication;
	use Edde\Api\Bus\Inject\RequestService;
	use Edde\Api\Log\Inject\LogService;
	use Edde\Api\Router\Inject\RouterService;
	use Edde\Common\Object\Object;

	class Application extends Object implements IApplication {
		use RequestService;
		use RouterService;
		use LogService;

		/** @inheritdoc */
		public function run(): int {
			try {
				if ($result = $this->requestService->execute($this->routerService->createRequest())) {
					return $result->getAttribute('code', 0);
				}
				return 0;
			} catch (AbortException $exception) {
				if ($previous = $exception->getPrevious()) {
					$this->logService->exception($previous, [
						'edde',
						'exception',
						'application',
					]);
				}
				echo $exception->getMessage();
				/**
				 * when there is an abort exception, it's code is used as primary response
				 */
				return ($code = $exception->getCode()) === 0 ? -1 : $code;
			} catch (\Throwable $exception) {
				$this->logService->exception($exception, [
					'edde',
					'exception',
					'application',
				]);
				return ($code = $exception->getCode()) === 0 ? -1 : $code;
			}
		}
	}
