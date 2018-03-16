<?php
	declare(strict_types=1);
	namespace Edde\Node;

	/**
	 * I don't know, if or why communication exceptions are evil or bad practice,
	 * but in this case it's quite useful language construct I have...
	 */
	class SkipException extends NodeException {
	}
