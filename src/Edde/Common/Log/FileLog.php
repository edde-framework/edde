<?php
	declare(strict_types=1);

	namespace Edde\Common\Log;

	use Edde\Api\File\IFile;
	use Edde\Api\Log\ILog;
	use Edde\Api\Log\ILogRecord;
	use Edde\Api\Log\LazyLogDirectoryTrait;

	/**
	 * Default file based log.
	 */
	class FileLog extends AbstractLog {
		use LazyLogDirectoryTrait;
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var IFile
		 */
		protected $file;

		/**
		 * A blonde, wanting to earn some money, decided to hire herself out as a handyman-type and started canvassing a wealthy neighborhood.
		 * She went to the front door of the first house and asked the owner if he had any jobs for her to do.
		 * "Well, you can paint my porch. How much will you charge?"
		 * The blonde said "How about 50 dollars?"
		 * The man agreed and told her that the paint and other materials that she might need were in the garage.
		 * The man's wife, inside the house, heard the conversation and said to her husband, "Does she realize that the porch goes all the way around the house?"
		 * The man replied, "She should, she was standing on it."
		 * A short time later, the blonde came to the door to collect her money.
		 * "You're finished already?" he asked.
		 * "Yes," the blonde answered, "and I had paint left over, so I gave it two coats."
		 * Impressed, the man reached in his pocket for the $50.
		 * "And by the way," the blonde a dded, "it's not a Porch, it's a Ferrari."
		 *
		 * @param string $name
		 */
		public function __construct(string $name) {
			$this->name = $name;
		}

		/**
		 * @param ILogRecord $logRecord
		 *
		 * @return ILog
		 */
		public function record(ILogRecord $logRecord): ILog {
			$this->file->write(sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $logRecord->getLog()));
			return $this;
		}

		protected function handleInit() {
			parent::handleInit();
			$this->logDirectory->create();
			$this->file = $this->logDirectory->file(date('Y-m-d-') . $this->name . '.log');
			$this->file->openForAppend();
		}
	}
