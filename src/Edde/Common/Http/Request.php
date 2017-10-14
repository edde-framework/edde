<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

		use Edde\Api\Http\ICookies;
		use Edde\Api\Http\IHeaders;
		use Edde\Api\Http\IRequest;
		use Edde\Api\Url\IUrl;
		use Edde\Common\Url\Url;

		/**
		 * A small boy was awoken in the middle of the night by strange noises from his parents’ room, and he decided to investigate.
		 * As he entered their bedroom, he was shocked to see his mom and dad shagging for all they were worth.
		 * “DAD!” he shouted. “What are you doing?”
		 * “It’s ok,” his father replied. “Your mother wants a baby, that’s all.”
		 * The small boy, excited at the prospect of a new baby brother, was pleased and went back to bed with a smile on his face.
		 *
		 * Several weeks later, the little boy was walking past the bathroom and was shocked to discover his mother giving oral gratification to his
		 * father.
		 * “DAD!” he shouted. “What are you doing now?”
		 * “Son, there’s been a change of plan,” his father replied.
		 * “Your mother did want a baby, but now she wants a BMW.”
		 */
		class Request extends AbstractHttp implements IRequest {
			/**
			 * @var IUrl
			 */
			protected $url;
			/**
			 * @var string
			 */
			protected $method;
			/**
			 * @var string|null
			 */
			protected $remoteAddress;
			/**
			 * @var string|null
			 */
			protected $remoteHost;
			/**
			 * @var IUrl
			 */
			protected $referer;

			/**
			 * @param IUrl     $url
			 * @param IHeaders $headers
			 * @param ICookies $cookies
			 */
			public function __construct(IUrl $url, IHeaders $headers, ICookies $cookies) {
				parent::__construct($headers, $cookies);
				$this->url = $url;
			}

			/**
			 * @inheritdoc
			 */
			public function setMethod(string $method): IRequest {
				/**
				 * here is safe to use PHP function because $method is (should be) simple string
				 */
				$this->method = strtoupper($method);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getMethod(): string {
				return $this->method ?: $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
			}

			/**
			 * @inheritdoc
			 */
			public function isMethod(string $method): bool {
				return strcasecmp($this->getMethod(), $method) === 0;
			}

			/**
			 * @inheritdoc
			 */
			public function getRemoteAddress(): ?string {
				return $this->remoteAddress ?: $this->remoteAddress = $_SERVER['REMOTE_ADDR'];
			}

			/**
			 * @inheritdoc
			 */
			public function getRemoteHost(): ?string {
				return $this->remoteHost === null && $this->remoteAddress !== null ? $this->remoteHost = gethostbyaddr($this->remoteAddress) : null;
			}

			/**
			 * @inheritdoc
			 */
			public function getUrl(): IUrl {
				return $this->url;
			}

			/**
			 * @inheritdoc
			 */
			public function getReferer(): ?IUrl {
				return $this->referer === null && $this->headers->has('referer') ? $this->referer = Url::create($this->headers->get('referer')) : null;
			}

			/**
			 * @inheritdoc
			 */
			public function isSecured(): bool {
				return $this->url->getScheme() === 'https';
			}
		}
