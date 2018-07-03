<?php
	declare(strict_types=1);

	namespace Edde\Ext\Application;

	use Edde\Common\Converter\Content;

	class ExceptionResponse extends Content {
		public function __construct(\Exception $exception) {
			parent::__construct($exception, 'exception');
		}
	}
