<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Common\Application\Response;

	/**
	 * Redirect implementation for controls.
	 */
	trait RedirectTrait {
		public function redirect($redirect) {
			$this->use();
			$this->responseManager->response(new Response('redirect', $this->linkFactory->link($redirect)));
		}
	}
