<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Configurable\IConfigurable;

	interface IJobManager extends IConfigurable {
		/**
		 * executes infinite loop
		 *
		 * @return IJobManager
		 */
		public function run(): IJobManager;

		/**
		 * run one job if any
		 *
		 * @return IJobManager
		 */
		public function tick(): IJobManager;

		/**
		 * this method is called automagically in run, but it's necessary
		 * to call it manually in other cases
		 *
		 * @return IJobManager
		 */
		public function startup(): IJobManager;

		/**
		 * this method is called automagically in run, but it's necessary
		 * to call it manually in other cases
		 *
		 * @return IJobManager
		 */
		public function shutdown(): IJobManager;
	}
