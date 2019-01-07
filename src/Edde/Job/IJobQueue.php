<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Configurable\IConfigurable;
	use Edde\Message\IPacket;
	use Edde\Storage\EmptyEntityException;
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
		 * enqueue a job (change it's state to ENQUEUED); this method should NOT be executed
		 * in parallel as it's not thread safe
		 *
		 * @return IEntity
		 *
		 * @throws EmptyEntityException
		 */
		public function enqueue(): IEntity;

		/**
		 * cleanup all (scheduled) jobs in a (persistent) queue
		 *
		 * @return IJobQueue
		 */
		public function cleanup(): IJobQueue;
	}
