<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Schema\UuidSchema;

	interface JobSchema extends UuidSchema {
		const alias = true;
		/**
		 * a job is in queue; it's waiting for pickup (producer side)
		 */
		const STATE_ENQUEUED = 0;
		/**
		 * when message has been reset due restart or so (originally RUNNING messages)
		 */
		const STATE_RESET = 1;
		/**
		 * a job is scheduled and an executor should run the job (daemon side)
		 */
		const STATE_SCHEDULED = 2;
		/**
		 * a job has been started by a worker (worker side)
		 */
		const STATE_RUNNING = 3;
		/**
		 * a job has been successfully done (worker side)
		 */
		const STATE_SUCCESS = 4;
		/**
		 * a job has failed (worker/daemon side)
		 */
		const STATE_FAILED = 5;

		/**
		 * job state
		 */
		public function state($default = self::STATE_ENQUEUED): int;

		/**
		 * when a message should be executed
		 */
		public function schedule(): DateTime;

		/**
		 * message to be processed
		 */
		public function message($type = 'json');

		/**
		 * timestamp of last change; if state is 0, than job has been created by
		 * "stamp" time and so on
		 */
		public function stamp(): DateTime;

		public function result(): ?string;

		public function runtime(): ?float;
	}
