<?php
	declare(strict_types=1);
	namespace Edde\Common\Application;

		use Edde\Api\Application\IApplication;
		use Edde\Api\Log\Inject\LogService;
		use Edde\Api\Request\Inject\RequestService;
		use Edde\Common\Object\Object;

		class Application extends Object implements IApplication {
			use RequestService;
			use LogService;

			/**
			 * @inheritdoc
			 */
			public function run(): int {
				try {
					/**
					 * the flow is simple:
					 * - router service is responsible for "global space" request resolution (are we cli? Are we in http mode? Is there a devil kitten?, ...)
					 * - we have request, so it's time to translate it into response (just data, computation, no output)
					 * - and at the end we can send http headers, if required, echo the things, do heavy processing, do output, kill devil kitten, whatever, ...
					 */
					return $this->requestService->execute()->getExitCode();
				} catch (\Throwable $exception) {
					$this->logService->exception($exception, [
						'edde',
						'application',
					]);
					return ($code = $exception->getCode()) === 0 ? -1 : $code;
				}
			}
		}
