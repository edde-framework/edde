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

	class JobQueue extends Edde implements IJobQueue {
		use Storage;
		use MessageBus;

		/** @inheritdoc */
		public function packet(IPacket $packet, DateTime $time = null): IEntity {
			return $this->storage->insert(new Entity(JobSchema::class, [
				'stamp'  => $time ?? new DateTime(),
				'packet' => $packet->export(),
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
				$job = $this->storage->single(JobSchema::class, '
					SELECT
						*
					FROM
						j:schema
					WHERE
						stamp <= NOW()
					ORDER BY
						stamp ASC
					LIMIT
						1
				', [
					'$query' => [
						'j' => JobSchema::class,
					],
				]);
				$this->storage->delete($job);
			} catch (EmptyEntityException $exception) {
				throw new HolidayException('Nothing to do, bro!');
			}
			return $job;
		}

		/** @inheritdoc */
		public function cleanup(): IJobQueue {
			$this->storage->fetch('
				DELETE FROM s:schema
			', [
				'$query' => [
					's' => JobSchema::class,
				],
			]);
			return $this;
		}
	}
