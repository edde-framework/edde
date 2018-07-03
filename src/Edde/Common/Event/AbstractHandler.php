<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\IHandler;
	use Edde\Common\AbstractObject;

	/**
	 * Common stuff for event handlers.
	 */
	abstract class AbstractHandler extends AbstractObject implements IHandler {
		/**
		 * @var string
		 */
		protected $scope;

		/**
		 * Q: Why did the shark keep swimming in circles?
		 *
		 * A: It had a nosebleed.
		 *
		 * @param string $scope
		 */
		public function __construct(string $scope = null) {
			$this->scope = $scope;
		}

		/**
		 * @inheritdoc
		 */
		public function getScope() {
			return $this->scope;
		}
	}
