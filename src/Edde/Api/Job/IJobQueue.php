<?php
	declare(strict_types=1);

	namespace Edde\Api\Job;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Protocol\IElement;

	/**
	 * Job queue is basically list of Elements that would be executed as a job (in separate thread usually).
	 *
	 * As a job means in different thread, but in the same way, so it would go through Protocol Service.
	 */
	interface IJobQueue extends IConfigurable {
		/**
		 * enqueue the given job
		 *
		 * @param IElement $element
		 *
		 * @return IJobQueue
		 */
		public function queue(IElement $element): IJobQueue;

		/**
		 * @param IElement[] $elementList
		 *
		 * @return IJobQueue
		 */
		public function queueList($elementList): IJobQueue;

		/**
		 * are there some pending jobs?
		 *
		 * @return bool
		 */
		public function hasJob(): bool;

		/**
		 * return generator/traversable of jobs
		 *
		 * @return \Traversable|\Iterator|IElement[]
		 */
		public function dequeue();
	}
