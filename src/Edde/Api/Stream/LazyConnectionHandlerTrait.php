<?php
	declare(strict_types=1);

	namespace Edde\Api\Stream;

	trait LazyConnectionHandlerTrait {
		/**
		 * @var IConnectionHandler
		 */
		protected $connectionHandler;

		public function lazyConnectionHandler(IConnectionHandler $connectionHandler) {
			$this->connectionHandler = $connectionHandler;
		}
	}
