<?php
	namespace Edde\Api\Database\Inject;

		use Edde\Api\Database\IEngine;

		trait Engine {
			/**
			 * @var IEngine
			 */
			protected $engine;

			/**
			 * @param IEngine $engine
			 */
			public function lazyEngine(IEngine $engine) {
				$this->engine = $engine;
			}
		}
