<?php
	declare(strict_types=1);

	namespace Edde\Common\Log\Event;

	use Edde\Api\Log\ILogRecord;

	/**
	 * Log record event.
	 */
	class LogRecordEvent extends LogEvent {
		/**
		 * @var ILogRecord
		 */
		protected $logRecord;

		/**
		 * A guy walks into a pub and sees a sign hanging over the bar that reads:
		 * CHEESEBURGER: $1.50
		 * CHICKEN SANDWICH: $2.50
		 * HAND JOB: $10.00
		 *
		 * He walks up to the bar and beckons one of the three exceptionally attractive blondes serving drinks.
		 * "Can I help you?" she asks.
		 * "I was wondering," whispers the man. "Are you the one who gives the hand jobs?"
		 * "Yes," she purrs. "I am."
		 * The man replies, "Well, wash your hands. I want a cheeseburger."
		 *
		 * @param ILogRecord $logRecord
		 */
		public function __construct(ILogRecord $logRecord) {
			parent::__construct();
			$this->logRecord = $logRecord;
		}

		/**
		 * @return ILogRecord
		 */
		public function getLogRecord(): ILogRecord {
			return $this->logRecord;
		}
	}
