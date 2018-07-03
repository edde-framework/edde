<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Collection\IList;
	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Common\Collection\AbstractDefferedList;
	use Edde\Common\Deffered\DefferedTrait;

	/**
	 * Session section for simple session data manipulation.
	 */
	class Session extends AbstractDefferedList implements ISession {
		use DefferedTrait;
		/**
		 * @var ISessionManager
		 */
		protected $sessionManager;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param ISessionManager $sessionManager
		 * @param string $name
		 */
		public function __construct(ISessionManager $sessionManager, string $name) {
			$this->sessionManager = $sessionManager;
			$this->name = $name;
		}

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): IList {
			$this->use();
			if ($value === null) {
				return parent::remove($name);
			}
			return parent::set($name, $value);
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->list = &$this->sessionManager->session($this->name);
		}
	}
