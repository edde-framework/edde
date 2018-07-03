<?php
	declare(strict_types = 1);

	namespace Edde\Common\Upgrade;

	/**
	 * This can be used as an adhoc upgrade.
	 */
	class CallbackUpgrade extends AbstractUpgrade {
		/**
		 * @var callable
		 */
		protected $callback;

		/**
		 * A guy goes into a tattoo parlor and asks for a tattoo of a $100 bill on his penis.
		 * Curious, the tattoo artist asks him why he would possibly want that.
		 * "Three reasons:
		 * I like to play with my money,
		 * I like to watch my money grow,
		 * - and a hundred dollars seems to be the only thing my wife will blow these days."
		 *
		 * @param callable $callback
		 * @param string $version
		 */
		public function __construct(callable $callback, $version) {
			parent::__construct($version);
			$this->callback = $callback;
		}

		/**
		 * @inheritdoc
		 */
		protected function onUpgrade() {
			/** @noinspection VariableFunctionsUsageInspection */
			call_user_func($this->callback);
		}
	}
