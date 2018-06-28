<?php
	declare(strict_types=1);
	namespace Edde\Http;

	use Edde\Url\IUrl;

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
		/** @var IUrl */
		protected $url;
		/** @var string */
		protected $method;

		/**
		 * @param IUrl     $url
		 * @param string   $method
		 * @param IHeaders $headers
		 */
		public function __construct(IUrl $url, string $method, IHeaders $headers) {
			parent::__construct($headers);
			$this->url = $url;
			$this->method = $method;
		}

		/** @inheritdoc */
		public function getMethod(): string {
			return $this->method;
		}

		/** @inheritdoc */
		public function getUrl(): IUrl {
			return $this->url;
		}
	}
