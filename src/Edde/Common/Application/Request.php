<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IRequest;
	use Edde\Common\AbstractObject;
	use Edde\Common\Strings\StringUtils;

	class Request extends AbstractObject implements IRequest {
		/**
		 * @var string
		 */
		protected $type;
		/**
		 * @var array
		 */
		protected $action;
		/**
		 * @var array
		 */
		protected $handle;
		/**
		 * @var string
		 */
		protected $id;

		/**
		 * What is the difference between a snowman and a snowwoman?
		 * -
		 * Snowballs.
		 *
		 * @param string $type
		 */
		public function __construct(string $type) {
			$this->type = $type;
		}

		public function getType(): string {
			return $this->type;
		}

		public function registerActionHandler(string $control, string $action, array $parameterList = []): IRequest {
			$this->action = [
				$control,
				$action,
				$parameterList,
			];
			return $this;
		}

		public function hasAction(): bool {
			return $this->action !== null;
		}

		public function getAction(): array {
			return $this->action;
		}

		public function getActionName(): string {
			return StringUtils::recamel($this->action[1], '-', 1);
		}

		public function registerHandleHandler(string $control, string $handle, array $parameterList = []): IRequest {
			$this->handle = [
				$control,
				$handle,
				$parameterList,
			];
			return $this;
		}

		public function hasHandle(): bool {
			return $this->handle !== null;
		}

		public function getHandle(): array {
			return $this->handle;
		}

		public function getHandleName(): string {
			return StringUtils::recamel($this->handle[1], '-', 1);
		}

		public function getCurrent(): array {
			if ($this->hasHandle()) {
				return $this->getHandle();
			} else if ($this->hasAction()) {
				return $this->getAction();
			}
			throw new ApplicationException(sprintf('Request has no action or handle. Ooops!'));
		}

		public function getCurrentName(): string {
			return StringUtils::recamel($this->getCurrent()[1], '-', 1);
		}

		public function getId(): string {
			if ($this->id === null) {
				$action = $this->action;
				$handle = $this->handle;
				unset($action[2], $handle[2]);
				$this->id = hash('sha256', json_encode($action) . json_encode($handle));
			}
			return $this->id;
		}
	}
