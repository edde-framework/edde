<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Edde;
	use Edde\Message\IMessage;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\Entity;
	use Edde\Storage\IEntity;

	class JobQueue extends Edde implements IJobQueue {
		use Storage;

		/** @inheritdoc */
		public function push(IMessage $message, DateTime $time = null): IJobQueue {
			$this->storage->insert(new Entity(JobSchema::class, [
				'stamp'   => $time ?? new DateTime(),
				'message' => $message->export(),
			]));
			return $this;
		}

		/** @inheritdoc */
		public function enqueue(): IEntity {
			$job = $this->storage->single(JobSchema::class, '
				SELECT
					*
				FROM
					j:schema
				WHERE
					state = :state
				ORDER BY
					stamp ASC
				LIMIT
					1
			', [
				'$query' => [
					'j' => JobSchema::class,
				],
				'state'  => JobSchema::STATE_CREATED,
			]);
			$job['state'] = JobSchema::STATE_ENQUEUED;
			$this->storage->update($job);
			return $job;
		}

		/** @inheritdoc */
		public function execute(string $message): IJobQueue {
			return $this;
		}
	}
