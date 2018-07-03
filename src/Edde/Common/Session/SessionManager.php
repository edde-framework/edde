<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Session\IFingerprint;
	use Edde\Api\Session\ISession;
	use Edde\Api\Session\ISessionManager;
	use Edde\Api\Session\LazySessionDirectoryTrait;
	use Edde\Api\Session\SessionException;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * Session manager is... session managing tool ;). It's responsible for whole session lifetime and section
	 * assigment (and collision preventing).
	 */
	class SessionManager extends AbstractDeffered implements ISessionManager {
		use LazyHttpResponseTrait;
		use LazySessionDirectoryTrait;
		/**
		 * @var IFingerprint
		 */
		protected $fingerprint;
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
		 * @param IFingerprint $fingerprint
		 */
		public function __construct(IFingerprint $fingerprint) {
			$this->fingerprint = $fingerprint;
			$this->namespace = 'edde';
		}

		/**
		 * @inheritdoc
		 */
		public function getSession(string $name): ISession {
			return $this->sessionList[$name] ?? $this->sessionList[$name] = new Session($this, $name);
		}

		/**
		 * @inheritdoc
		 * @throws SessionException
		 */
		public function &session(string $name): array {
			$this->use();
			$this->start();
			/** @noinspection PhpVariableNamingConventionInspection */
			$_SESSION[$this->namespace] = $_SESSION[$this->namespace] ?? [];
			/** @noinspection PhpVariableNamingConventionInspection */
			$_SESSION[$this->namespace][$name] = $_SESSION[$this->namespace][$name] ?? [];
			return $_SESSION[$this->namespace][$name];
		}

		/**
		 * @inheritdoc
		 * @throws SessionException
		 */
		public function start(): ISessionManager {
			if ($this->isSession()) {
				return $this;
			}
			session_save_path($this->sessionDirectory->getDirectory());
			if (($fingerprint = $this->fingerprint->fingerprint()) !== null) {
				session_id($fingerprint);
			}
			session_start();
			$headerList = $this->httpResponse->getHeaderList();
			foreach (headers_list() as $header) {
				list($name, $header) = explode(':', $header, 2);
				$headerList->set(trim($name), trim($header));
			}
			if (headers_sent($file, $line)) {
				throw new SessionException(sprintf('Cannot handle session start: somebody has already sent headers from [%s at %d].', $file, $line));
			}
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
				throw new SessionException('Session is not running; there is nothing to close.');
			}
			session_write_close();
			return $this;
		}
	}
