<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Configurable\IConfigurable;
	use Edde\Message\IMessage;
	use Edde\Storage\IEntity;

	interface IJobQueue extends IConfigurable {
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
		 * schedule the given message; $diff is DateInterval parameter
		 *
		 * @param IMessage $message
		 * @param string   $diff
		 *
		 * @return IEntity
		 */
		public function schedule(IMessage $message, string $diff): IEntity;

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
		 * @return IEntity updated job
		 */
		public function state(string $job, int $state, string $result = null): IEntity;

		/**
		 * update the given job
		 *
		 * @param IEntity $entity
		 *
		 * @return IEntity
		 */
		public function update(IEntity $entity): IEntity;

		/**
		 * get a job by the given uuid
		 *
		 * @param string $uuid
		 *
		 * @return IEntity
		 */
		public function byUuid(string $uuid): IEntity;

		/**
		 * return number of allocated jobs; this could be used for parallel
		 * job limitation
		 *
		 * @return int
		 */
		public function countLimit(): int;

		/**
		 * reset "dead" jobs; this should be used when one is sure there is nothing actually
		 * running, for example after a container restart; thus there are "dead" jobs in the queue
		 * which has to be rescheduled
		 *
		 * @return IJobQueue
		 */
		public function reset(): IJobQueue;

		/**
		 * cleanup all (scheduled) jobs in a (persistent) queue
		 *
		 * @return IJobQueue
		 */
		public function cleanup(): IJobQueue;

		/**
		 * return array of state counts
		 *
		 * @return array
		 */
		public function stats(): array;
	}
