<?php
	declare(strict_types=1);

	namespace Edde\Api\Thread;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Thread manager is general service to work with threaded jobs.
	 */
	interface IThreadManager extends IConfigurable {
		/**
		 * execute the thread (should be safe to be called at any time)
		 *
		 * @param array|null $parameterList
		 *
		 * @return IThreadManager
		 */
		public function execute(array $parameterList = null): IThreadManager;

		/**
		 * pool is basically same as an ::dequeue() but it takes care about number of threads
		 *
		 * @return IThreadManager
		 */
		public function pool(): IThreadManager;

		/**
		 * set maximum number of concurrent threads on this "node" (as an application - webserver/cli/...)
		 *
		 * This value should be used wisely because if there is web based executor and high number of threads, they
		 * can eat all webserver's workers.
		 *
		 * @param int $maximumThreadCount
		 *
		 * @return IThreadManager
		 */
		public function setMaximumThreadCount(int $maximumThreadCount): IThreadManager;
	}
