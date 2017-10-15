<?php
	declare(strict_types=1);
	namespace Edde\Api\Converter;

		use Edde\Api\Config\IConfigurable;

		/**
		 * Implementation of general conversion mechanism which is useful
		 * for example on content negotiation during http request - an user
		 * does not take care about incoming data, it's enough to say, gimme
		 * array, and the magic in converter do the job. If it's possible :).
		 */
		interface IConverterManager extends IConfigurable {
		}
