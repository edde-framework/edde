<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Configurable\IConfigurable;
	use Edde\Message\MessageException;

	interface IJobManager extends IConfigurable {
		/**
		 * executes infinite loop
		 *
		 * @return IJobManager
		 *
		 * @throws JobException
		 */
		public function run(): IJobManager;

		/**
		 * run one job if any
		 *
		 * @return IJobManager
		 *
		 * @throws HolidayException
		 * @throws JobException
		 * @throws MessageException
		 */
		public function tick(): IJobManager;

		/**
		 * @return bool
		 */
		public function isPaused(): bool;

		/**
		 * @param bool $pause
		 *
		 * @return IJobManager
		 */
		public function pause(bool $pause = true): IJobManager;
	}
