<?php
	declare(strict_types=1);
	namespace Edde\Service\Application;

	use Edde\Common\Log\SimpleLog;
	use Edde\Exception\Router\BadRequestException;
	use Edde\Inject\Application\Application;
	use Edde\Inject\Log\LogService;
	use Edde\Log\ILogRecord;
	use Edde\TestCase;

	class ApplicationTest extends TestCase {
		use Application;
		use LogService;

		public function testRun() {
			$this->expectException(BadRequestException::class);
			$this->expectExceptionMessage('Cannot handle current request.');
			$this->logService->registerLog($log = new SimpleLog(), ['exception']);
			$this->application->run();
			/** @var $logs ILogRecord[] */
			self::assertNotEmpty($logs = $log->getLogRecords());
			list($record) = $logs;
			throw $record->getLog();
		}
	}
