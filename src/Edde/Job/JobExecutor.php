<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Message\IPacket;

	class JobExecutor extends Edde implements IJobExecutor {
		/** @inheritdoc */
		public function execute(IPacket $packet): IJobExecutor {
			return $this;
		}
	}
