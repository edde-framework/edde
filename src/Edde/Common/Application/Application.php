<?php
	declare(strict_types=1);
	namespace Edde\Common\Application;

		use Edde\Api\Application\Exception\AbortException;
		use Edde\Api\Application\IApplication;
		use Edde\Api\Log\Inject\LogService;
		use Edde\Api\Request\Inject\RequestService;
		use Edde\Common\Object\Object;

		class Application extends Object implements IApplication {
			use RequestService;
			use LogService;
			protected $exitCode = 0;

			/**
			 * @inheritdoc
			 */
			public function setExitCode(int $exitCode): IApplication {
				$this->exitCode = $exitCode;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function run(): int {
				try {
					$this->requestService->execute();
					return $this->exitCode;
				} catch (AbortException $exception) {
					if ($previous = $exception->getPrevious()) {
						$this->logService->exception($previous, [
							'edde',
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
						'application',
					]);
					return ($code = $exception->getCode()) === 0 ? -1 : $code;
				}
			}
		}
