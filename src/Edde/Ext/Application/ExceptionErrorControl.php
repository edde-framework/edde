<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Application;

	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Router\RouterException;
	use Edde\Common\Html\ViewControl;

	/**
	 * Only rethrows exception.
	 */
	class ExceptionErrorControl extends ViewControl implements IErrorControl {
		/**
		 * @inheritdoc
		 * @throws \Exception
		 */
		public function exception(\Exception $e) {
			if ($this->httpRequest->isAjax()) {
				throw $e;
			}
			try {
				throw $e;
			} catch (RouterException $e) {
				$this->snippet(__DIR__ . '/template/404.xml');
				$this->response();
			} catch (\Exception $e) {
				$this->snippet(__DIR__ . '/template/500.xml');
				$this->response();
			}
		}
	}
