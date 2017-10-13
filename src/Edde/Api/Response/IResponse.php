<?php
	declare(strict_types=1);
	namespace Edde\Api\Response;

		use Edde\Api\Content\IContent;

		interface IResponse {
			/**
			 * get a content of the response
			 *
			 * @return IContent
			 */
			public function getContent(): IContent;

			/**
			 * if necessary, set the application exit code
			 *
			 * @param int $exitCode
			 *
			 * @return IResponse
			 */
			public function setExitCode(int $exitCode): IResponse;

			/**
			 * even an application is executed in http mode, it could return
			 * a return code
			 *
			 * @return int
			 */
			public function getExitCode(): int;
		}
