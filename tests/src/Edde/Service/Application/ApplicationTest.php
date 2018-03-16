<?php
	declare(strict_types=1);
	namespace Edde\Service\Application;

	use Edde\Api\Log\ILogRecord;
	use Edde\Api\Log\Inject\LogService;
	use Edde\Api\Router\Exception\BadRequestException;
	use Edde\Common\Log\SimpleLog;
	use Edde\Ext\Test\TestCase;
	use Edde\Inject\Application\Application;

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
