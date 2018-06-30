<?php
	declare(strict_types=1);
	namespace Edde\Log;

	use Edde\File\Directory;
	use Edde\File\File;
	use Edde\Service\Log\LogService;
	use Edde\TestCase;
	use Exception;
	use function file_get_contents;
	use function in_array;

	class LogServiceTest extends TestCase {
		use LogService;

		public function testLogTags() {
			$this->logService->registerLogger(new class() extends AbstractLogger {
				protected $file;

				public function __construct() {
					$this->file = new File(__DIR__ . '/temp/log.log');
					$this->file->open('w+');
				}

				public function record(ILog $log, array $tags = []): void {
					if (in_array('yep', $tags)) {
						$this->file->write($log->getLog());
					}
					if (in_array('info', $tags)) {
						$this->file->write('info: ' . $log->getLog());
					}
				}
			});
			$this->logService->log("foo\n", ['yep']);
			$this->logService->log("bar\n", ['nope']);
			$this->logService->info("nothing intetesting\n");
			self::assertEquals("foo\ninfo: nothing intetesting\n", file_get_contents(__DIR__ . '/temp/log.log'));
		}

		public function testLogStdErr() {
			$this->logService->registerLogger($logger = new class() extends AbstractLogger {
				public $logs = [];

				public function record(ILog $log, array $tags = []): void {
					if (in_array('stderr', $tags)) {
						$this->logs[] = $log->getLog();
					}
				}
			});
			$this->logService->stderr('boom');
			self::assertEquals(['boom'], $logger->logs);
		}

		public function testLogStdOut() {
			$this->logService->registerLogger($logger = new class() extends AbstractLogger {
				public $logs = [];

				public function record(ILog $log, array $tags = []): void {
					if (in_array('stdout', $tags)) {
						$this->logs[] = $log->getLog();
					}
				}
			});
			$this->logService->stdout('muhaha');
			self::assertEquals(['muhaha'], $logger->logs);
		}

		public function testLogWarning() {
			$this->logService->registerLogger($logger = new class() extends AbstractLogger {
				public $logs = [];

				public function record(ILog $log, array $tags = []): void {
					if (in_array('warning', $tags)) {
						$this->logs[] = $log->getLog();
					}
				}
			});
			$this->logService->warning('whip?');
			self::assertEquals(['whip?'], $logger->logs);
		}

		public function testLogError() {
			$this->logService->registerLogger($logger = new class() extends AbstractLogger {
				public $logs = [];

				public function record(ILog $log, array $tags = []): void {
					if (in_array('error', $tags)) {
						$this->logs[] = $log->getLog();
					}
				}
			});
			$this->logService->error('booooom!');
			self::assertEquals(['booooom!'], $logger->logs);
		}

		public function testLogCritical() {
			$this->logService->registerLogger($logger = new class() extends AbstractLogger {
				public $logs = [];

				public function record(ILog $log, array $tags = []): void {
					if (in_array('critical', $tags)) {
						$this->logs[] = $log->getLog();
					}
				}
			});
			$this->logService->critical('something really baaad!');
			self::assertEquals(['something really baaad!'], $logger->logs);
		}

		public function testLogException() {
			$this->logService->registerLogger($logger = new class() extends AbstractLogger {
				public $logs = [];

				public function record(ILog $log, array $tags = []): void {
					if (in_array('exception', $tags)) {
						$this->logs[] = $log->getLog();
					}
				}
			});
			$this->logService->exception($excepton = new Exception('boom'));
			self::assertSame([$excepton], $logger->logs);
		}

		protected function setUp() {
			parent::setUp();
			$temp = new Directory(__DIR__ . '/temp');
			$temp->purge();
		}

		protected function tearDown() {
			parent::tearDown();
			$temp = new Directory(__DIR__ . '/temp');
			$temp->delete();
		}
	}
