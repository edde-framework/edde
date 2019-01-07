<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Configurable\IConfigurable;
	use Edde\Message\IMessage;
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
		public function packet(IPacket $packet, DateTime $time = null): IEntity;

		/**
		 * push a new message
		 *
		 * @param IMessage      $message
		 * @param DateTime|null $time
		 *
		 * @return IEntity
		 */
		public function message(IMessage $message, DateTime $time = null): IEntity;

		/**
		 * schedule the given packet; $diff is DateInterval parameter
		 *
		 * @param IPacket $packet
		 * @param string  $diff
		 *
		 * @return IEntity
		 */
		public function schedulePacket(IPacket $packet, string $diff): IEntity;

		/**
		 * schedule the given message; $diff is DateInterval parameter
		 *
		 * @param IMessage $message
		 * @param string   $diff
		 *
		 * @return IEntity
		 */
		public function scheduleMessage(IMessage $message, string $diff): IEntity;

		/**
		 * pick a job (should be removed from source queue)
		 *
		 * @return IEntity
		 *
		 * @throws HolidayException
		 */
		public function pick(): IEntity;

		/**
		 * update a state of the given job
		 *
		 * @param string $job
		 * @param int    $state
		 *
		 * @return IJobQueue
		 */
		public function state(string $job, int $state): IJobQueue;

		/**
		 * get a job by the given uuid
		 *
		 * @param string $uuid
		 *
		 * @return IEntity
		 */
		public function byUuid(string $uuid): IEntity;

		/**
		 * return count of jobs of the given state
		 *
		 * @param int $state
		 *
		 * @return int
		 */
		public function countState(int $state): int;

		/**
		 * cleanup all (scheduled) jobs in a (persistent) queue
		 *
		 * @return IJobQueue
		 */
		public function cleanup(): IJobQueue;
	}
