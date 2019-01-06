<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateTime;
	use Edde\Edde;
	use Edde\Message\IMessage;
	use Edde\Message\IPacket;
	use Edde\Service\Message\MessageBus;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\Entity;
	use Edde\Storage\IEntity;
	use Throwable;

	class JobQueue extends Edde implements IJobQueue {
		use Storage;
		use MessageBus;

		/** @inheritdoc */
		public function push(IMessage $message, DateTime $time = null): IEntity {
			return $this->storage->insert(new Entity(JobSchema::class, [
				'stamp'   => $time ?? new DateTime(),
				'message' => $message->export(),
			]));
		}

		/** @inheritdoc */
		public function enqueue(): IEntity {
			$job = $this->storage->single(JobSchema::class, '
				SELECT
					*
				FROM
					j:schema
				WHERE
					state = :state AND
					stamp <= NOW()
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
		public function execute(string $job): IPacket {
			$job = $this->byUuid($job);
			$job['state'] = JobSchema::STATE_RUNNING;
			$this->storage->update($job);
			try {
				return $this->messageBus->execute($this->messageBus->importMessage($job['message']));
			} catch (Throwable $exception) {
				throw $exception;
			} finally {
				$job['state'] = JobSchema::STATE_DONE;
				$this->storage->update($job);
			}
		}

		/** @inheritdoc */
		public function byUuid(string $uuid): IEntity {
			return $this->storage->load(JobSchema::class, $uuid);
		}
	}
