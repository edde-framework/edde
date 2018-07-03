<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\EventException;
	use Edde\Api\Event\IHandler;
	use Edde\Common\AbstractObject;
	use Edde\Common\Event\Handler\CallableHandler;
	use Edde\Common\Event\Handler\ReflectionHandler;

	/**
	 * Handler cache to hide implementation details about input handler.
	 */
	class HandlerFactory extends AbstractObject {
		/**
		 * create event handler based on input or throw an exception if input is not supported
		 *
		 * @param mixed $handler
		 * @param string|null $scope
		 *
		 * @return IHandler
		 * @throws EventException
		 */
		static public function handler($handler, string $scope = null): IHandler {
			if (is_callable($handler)) {
				return new CallableHandler($handler, $scope);
			} else if (is_object($handler)) {
				return new ReflectionHandler($handler, $scope);
			}
			throw new EventException(sprintf('Cannot create handler from type [%s].', gettype($handler)));
		}
	}
