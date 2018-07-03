<?php
	declare(strict_types=1);

	namespace Edde\Common\Log;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Log\ILog;
	use Edde\Api\Log\ILogRecord;
	use Edde\Api\Protocol\Event\LazyEventBusTrait;
	use Edde\Common\Log\Event\LogRecordEvent;

	/**
	 * Event based logged (log records are emitted as events).
	 *
	 * A blonde and a lawyer are seated next to each other on a flight from LA to NY.
	 * The lawyer asks if she would like to play a fun game?
	 * The blonde, tired, just wants to take a nap, politely declines and rolls over to the window to catch a few winks.
	 * The lawyer persists and explains that the game is easy and a lot of fun.
	 * He explains, "I ask you a question, and if you don't know the answer, you pay me $5.00, and vice versa.
	 * "Again, she declines and tries to get some sleep.
	 * The lawyer, now agitated, says, "Okay, if you don't know the answer you pay me $5.00, and if I don't know the answer, I will pay you $500.00."
	 * This catches the blonde's attention and, figuring there will be no end to this torment unless she plays, agrees to the game.
	 * The lawyer asks the first question.
	 * "What's the distance from the earth to the moon?"
	 * The blonde doesn't say a word, reaches into her purse, pulls out a $5.00 bill and hands it to the lawyer.
	 * "Okay" says the lawyer, "your turn."
	 * She asks the lawyer, "What goes up a hill with three legs and comes down with four legs?"
	 * The lawyer, puzzled, takes out his laptop computer and searches all his references, no answer.
	 * He taps into the air phone with his modem and searches the net and the library of congress, no answer. Frustrated, he sends e-mails to all his friends and coworkers, to no avail.
	 * After an hour, he wakes the blonde, and hands her $500.00.
	 * The blonde says, "Thank you," and turns back to get some more sleep.
	 * The lawyer, who is more than a little miffed, wakes the blonde and asks, "Well, what's the answer?"
	 * Without a word, the blonde reaches into her purse, hands the lawyer $5.00, and goes back to sleep.
	 * And you thought blondes were dumb.
	 */
	class EventLog extends AbstractLog {
		use LazyContainerTrait;
		use LazyEventBusTrait;

		/**
		 * @inheritdoc
		 */
		public function record(ILogRecord $logRecord): ILog {
			$this->eventBus->emit($this->container->create(LogRecordEvent::class, [$logRecord]));
			return $this;
		}
	}
