<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Configurable\IConfigurable;
	use Edde\Message\IMessage;
	use Edde\Storage\EmptyEntityException;
	use Edde\Storage\IEntity;

	interface IJobQueue extends IConfigurable {
		/**
		 * send a message into queue
		 *
		 * @param IMessage $message a message going to queue
		 * @param DateTime $time    if time is not set, message is executed "immediately" asynchronously
		 *
		 * @return IJobQueue
		 */
		public function push(IMessage $message, DateTime $time = null): IEntity;

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
		 * cleanup jobs already executed
		 *
		 * @return IJobQueue
		 */
		public function cleanup(): IJobQueue;

		/**
		 * get a job by an uuid
		 *
		 * @param string $uuid
		 *
		 * @return IEntity
		 */
		public function byUuid(string $uuid): IEntity;
	}
