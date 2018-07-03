<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Event;

	use Edde\Api\Protocol\Event\IEvent;
	use Edde\Common\Protocol\Element;

	class Event extends Element implements IEvent {
		public function __construct(string $event = null, array $data = [], string $id = null) {
			parent::__construct('event', $id);
			$this->setAttribute('event', $event ?: static::class);
			empty($data) === false ? $this->data($data) : null;
		}

		/**
		 * @inheritdoc
		 */
		public function getEvent(): string {
			return (string)$this->getAttribute('event');
		}
	}
