<?php
	declare(strict_types=1);

	namespace Edde\Common\Strings;

	use Edde\Api\Url\UrlException;
	use Edde\Common\Resource\Resource;
	use Edde\Common\Url\Url;

	/**
	 * String resource.
	 */
	class StringResource extends Resource {
		/**
		 * @var string
		 */
		protected $string;

		/**
		 * I wanted to grow my own food but I couldn’t get bacon seeds anywhere.
		 *
		 * @param string $string
		 *
		 * @throws UrlException
		 */
		public function __construct(string $string) {
			parent::__construct(Url::create('resource://string/' . sha1($string)));
			$this->string = $string;
		}

		/**
		 * @inheritdoc
		 */
		public function get() {
			return $this->string;
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			return StringUtils::createIterator($this->string);
		}
	}
