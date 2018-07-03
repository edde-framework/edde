<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Converter\IContent;

	interface IResponseHandler extends IConfigurable {
		/**
		 * execute the response
		 *
		 * @param IContent $content
		 *
		 * @return IResponseHandler
		 */
		public function send(IContent $content): IResponseHandler;
	}
