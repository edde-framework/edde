<?php
	declare(strict_types=1);

	namespace Edde\Common\Session;

	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Api\Session\LazyFingerprintTrait;
	use Edde\Api\Session\LazySessionDirectoryTrait;
	use Edde\Api\Session\SessionException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Http\HttpUtils;
	use Edde\Common\Object;

	/**
	 * Session manager is... session managing tool ;). It's responsible for whole session lifetime and section
	 * assignment (and collision preventing).
	 */
	class SessionManager extends Object implements ISessionManager {
		use LazyHttpResponseTrait;
		use LazySessionDirectoryTrait;
		use LazyFingerprintTrait;
		use ConfigurableTrait;
		/**
		 * @var string
		 */
		protected $namespace;
		/**
		 * @var ISession[]
		 */
		protected $sessionList = [];

		/**
		 * You know you've been online too long when:
		 *
		 * Your girlfriend says communication is important to her, so you buy another computer and install an instant messenger so the two of you can chat.
		 *
		 * @param string $namespace
		 */
		public function __construct(string $namespace = 'edde') {
			$this->namespace = $namespace;
		}

		/**
		 * @inheritdoc
		 */
		public function getSession(string $name): ISession {
			$this->start();
			return $this->sessionList[$name] ?? $this->sessionList[$name] = new Session($this->namespace, $name);
		}

		/**
		 * @inheritdoc
		 * @throws SessionException
		 */
		public function start(): ISessionManager {
			if ($this->isSession()) {
				return $this;
			}
			if (headers_sent($file, $line)) {
				throw new SessionException(sprintf('Cannot handle session start: somebody has already sent headers from [%s at %d].', $file, $line));
			}
			session_save_path($this->sessionDirectory->getDirectory());
			if (($fingerprint = $this->fingerprint->fingerprint()) !== null) {
				session_id($fingerprint);
			}
			if (@session_start() === false) {
				throw new SessionStartException('Cannot start session.');
			}
			$headerList = $this->httpResponse->getHeaderList();
			$headerList->put(HttpUtils::headerList(implode("\r\n", headers_list()), false));
			header_remove();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isSession(): bool {
			return session_status() === PHP_SESSION_ACTIVE;
		}

		/**
		 * @inheritdoc
		 */
		public function clear(): ISessionManager {
			foreach ($_SESSION[$this->namespace] as $name => &$section) {
				$section = [];
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws SessionException
		 */
		public function close(): ISessionManager {
			if ($this->isSession() === false) {
				throw new InactiveSessionException('Session is not running; there is nothing to close.');
			}
			session_write_close();
			session_unset();
			session_destroy();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			if ($this->isSession() === false) {
				throw new InactiveSessionException('Session is not running; session name cannot be retrieved.');
			}
			return session_name();
		}

		/**
		 * @inheritdoc
		 */
		public function getSessionId(): string {
			if ($this->isSession() === false) {
				throw new InactiveSessionException('Session is not running; cannot get session id.');
			}
			return session_id();
		}

		protected function handleSetup() {
			parent::handleSetup();
			$this->sessionDirectory->create();
		}
	}
