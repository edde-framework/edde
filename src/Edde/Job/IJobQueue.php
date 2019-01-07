<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Configurable\IConfigurable;
	use Edde\Message\IPacket;
	use Edde\Storage\IEntity;

	interface IJobQueue extends IConfigurable {
		/**
		 * send a message into queue
		 *
		 * @param IPacket  $packet a message going to queue
		 * @param DateTime $time   if time is not set, message is executed "immediately" asynchronously
		 *
		 * @return IJobQueue
		 */
		public function push(IPacket $packet, DateTime $time = null): IEntity;

		/**
		 * schedule the given packet; $diff is DateInterval parameter
		 *
		 * @param IPacket $packet
		 * @param string  $diff
		 *
		 * @return IEntity
		 */
		public function schedule(IPacket $packet, string $diff): IEntity;

		/**
		 * pick a job (should be removed from source queue)
		 *
		 * @return IEntity
		 *
		 * @throws HolidayException
		 */
		public function pick(): IEntity;

		/**
		 * cleanup all (scheduled) jobs in a (persistent) queue
		 *
		 * @return IJobQueue
		 */
		public function cleanup(): IJobQueue;
	}
