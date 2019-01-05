<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Edde;
	use Edde\Message\IMessage;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\Entity;

	class JobQueue extends Edde implements IJobQueue {
		use Storage;

		/** @inheritdoc */
		public function enqueue(IMessage $message, DateTime $time = null): IJobQueue {
			$this->storage->insert(new Entity(JobSchema::class, [
				'stamp'   => $time ?? new DateTime(),
				'message' => $message->export(),
			]));
			return $this;
		}

		/** @inheritdoc */
		public function execute(string $message): IJobQueue {
			return $this;
		}
	}
