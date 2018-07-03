<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\IJobQueue;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object\Object;

	abstract class AbstractJobQueue extends Object implements IJobQueue {
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function queueList($elementList): IJobQueue {
			foreach ($elementList as $element) {
				$this->queue($element);
			}
			return $this;
		}
	}
