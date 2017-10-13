<?php
	namespace Edde\Common\Content;

	/**
	 * Input content represent's php input stream.
	 */
		class InputContent extends Content {
			public function __construct(string $type) {
				parent::__construct('php://input', $type);
			}
		}
