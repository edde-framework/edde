<?php
	declare(strict_types=1);
	namespace Edde\Element;

	class Event extends Element implements IEvent {
		public function __construct(string $event, string $uuid) {
			parent::__construct('event', $uuid, ['event' => $event]);
		}

		/** @inheritdoc */
		public function getEvent(): string {
			return (string)$this->getAttribute('event');
		}
	}
