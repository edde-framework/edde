<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Configurable\IConfigurable;
	use Edde\Message\IPacket;

	interface IJobExecutor extends IConfigurable {
		/**
		 * execute should asynchronously execute given message (packet)
		 *
		 * @param IPacket $packet
		 *
		 * @return IJobExecutor
		 *
		 * @throws JobException if a job has not been successfully executed
		 */
		public function execute(IPacket $packet): IJobExecutor;
	}
