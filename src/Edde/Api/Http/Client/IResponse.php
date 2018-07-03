<?php
	declare(strict_types=1);

	namespace Edde\Api\Http\Client;

	use Edde\Api\Converter\IContent;
	use Edde\Api\Http\IResponse as IHttpResponse;

	interface IResponse extends IHttpResponse {
		/**
		 * convert the response to something in the target list
		 *
		 * @param array $targetList
		 *
		 * @return IContent
		 */
		public function convert(array $targetList): IContent;
	}
