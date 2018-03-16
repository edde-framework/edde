<?php
	declare(strict_types=1);
	namespace Edde\Ext\Router;

	use Edde\Element\IRequest;
	use Edde\Router\AbstractRouter;

	/**
	 * Static router all the times returns same request.
	 */
	class StaticRouter extends AbstractRouter {
		/** @var IRequest */
		protected $request;

		public function __construct(IRequest $request) {
			$this->request = $request;
		}

		/** @inheritdoc */
		public function canHandle(): bool {
			return true;
		}

		/** @inheritdoc */
		public function createRequest(): IRequest {
			return $this->request;
		}
	}
