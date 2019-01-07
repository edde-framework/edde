<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use DateInterval;
	use DateTime;
	use Edde\Edde;
	use Edde\Message\IMessage;
	use Edde\Message\IPacket;
	use Edde\Service\Message\MessageBus;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\EmptyEntityException;
	use Edde\Storage\Entity;
	use Edde\Storage\IEntity;
	use function date;

	class JobQueue extends Edde implements IJobQueue {
		use Storage;
		use MessageBus;

		/** @inheritdoc */
		public function packet(IPacket $packet, DateTime $time = null): IEntity {
			return $this->storage->insert(new Entity(JobSchema::class, [
				'schedule' => $time ?? new DateTime(),
				'stamp'    => new DateTime(),
				'packet'   => $packet->export(),
			]));
		}

		/** @inheritdoc */
		public function message(IMessage $message, DateTime $time = null): IEntity {
			return $this->packet($this->messageBus->createPacket()->message($message), $time);
		}

		/** @inheritdoc */
		public function schedulePacket(IPacket $packet, string $diff): IEntity {
			return $this->packet($packet, (new DateTime())->add(new DateInterval($diff)));
		}

		/** @inheritdoc */
		public function scheduleMessage(IMessage $message, string $diff): IEntity {
			return $this->message($message, (new DateTime())->add(new DateInterval($diff)));
		}

		/** @inheritdoc */
		public function pick(): IEntity {
			try {
				return $this->storage->single(JobSchema::class, '
					SELECT
						*
					FROM
						j:schema
					WHERE
						(
							state = :enqueued OR
							state = :reset
						) AND
						schedule <= :now
					ORDER BY
						schedule ASC
					LIMIT
						1
				', [
					'$query'   => [
						'j' => JobSchema::class,
					],
					'enqueued' => JobSchema::STATE_ENQUEUED,
					'reset'    => JobSchema::STATE_RESET,
					'now'      => date('c'),
				]);
			} catch (EmptyEntityException $exception) {
				throw new HolidayException('Nothing to do, bro!');
			}
		}

		/** @inheritdoc */
		public function state(string $job, int $state): IJobQueue {
			$job = $this->byUuid($job);
			$job['state'] = $state;
			$job['stamp'] = new DateTime();
			$this->storage->update($job);
			return $this;
		}

		/** @inheritdoc */
		public function byUuid(string $uuid): IEntity {
			return $this->storage->load(JobSchema::class, $uuid);
		}

		/** @inheritdoc */
		public function countLimit(): int {
			$count = 0;
			$query = '
				SELECT 
					COUNT(uuid) as count 
				FROM 
					s:schema 
				WHERE 
					state = :scheduled OR
					state = :running
			';
			foreach ($this->storage->value($query, ['scheduled' => JobSchema::STATE_SCHEDULED, 'running' => JobSchema::STATE_RUNNING, '$query' => ['s' => JobSchema::class]]) as $count) {
				break;
			}
			return $count;
		}

		/** @inheritdoc */
		public function reset(): IJobQueue {
			/**
			 * the simple phase is to reset jobs just scheduled
			 */
			$this->storage->fetch('
				UPDATE
					s:schema
				SET
					state = :enqueued,
					stamp = :stamp
				WHERE
					state = :scheduled
			', [
				'$query'    => [
					's' => JobSchema::class,
				],
				'enqueued'  => JobSchema::STATE_ENQUEUED,
				'scheduled' => JobSchema::STATE_SCHEDULED,
				'stamp'     => date('c'),
			]);
			/**
			 * a bit more complicated is to reset originally running jobs
			 */
			$this->storage->fetch('
				UPDATE
					s:schema
				SET
					state = :reset,
					stamp = :stamp
				WHERE
					state = :running
			', [
				'$query'  => [
					's' => JobSchema::class,
				],
				'reset'   => JobSchema::STATE_RESET,
				'running' => JobSchema::STATE_RUNNING,
				'stamp'   => date('c'),
			]);
			return $this;
		}

		/** @inheritdoc */
		public function cleanup(): IJobQueue {
			$this->storage->fetch('
				DELETE FROM s:schema WHERE state >= :state
			', [
				'state'  => JobSchema::STATE_SUCCESS,
				'$query' => [
					's' => JobSchema::class,
				],
			]);
			return $this;
		}

		/** @inheritdoc */
		public function stats(): array {
			$stats = [];
			$query = '
				SELECT
					(SELECT COUNT(uuid) FROM s:schema WHERE state = 0) AS enqueued,
					(SELECT COUNT(uuid) FROM s:schema WHERE state = 1) AS reset,
					(SELECT COUNT(uuid) FROM s:schema WHERE state = 2) AS scheduled,
					(SELECT COUNT(uuid) FROM s:schema WHERE state = 3) AS running,
					(SELECT COUNT(uuid) FROM s:schema WHERE state = 4) AS success,
					(SELECT COUNT(uuid) FROM s:schema WHERE state = 5) AS failed
			';
			foreach ($this->storage->fetch($query, ['$query' => ['s' => JobSchema::class]]) as $stats) {
				break;
			}
			return $stats;
		}
	}
