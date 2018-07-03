<?php
	declare(strict_types=1);

	namespace Edde\Api\Router;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Protocol\IElement;

	interface IRouter extends IConfigurable {
		/**
		 * create request must create IElement as it is a general way how to send "something" to the application; so in this
		 * case "request" means "general request to an application, even it could be an event"
		 *
		 * @return IElement|null
		 */
		public function createRequest();
	}
