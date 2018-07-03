<?php
	declare(strict_types=1);

	namespace Edde\Common\Link;

	use Edde\Api\Link\ILink;
	use Edde\Common\Object;

	class Link extends Object implements ILink {
		/**
		 * @var mixed
		 */
		protected $link;
		/**
		 * @var array
		 */
		protected $parameterList;

		/**
		 * A naked women robbed a bank. Nobody could remember her face.
		 *
		 * @param mixed $link
		 * @param array $parameterList
		 */
		public function __construct($link, array $parameterList = []) {
			$this->link = $link;
			$this->parameterList = $parameterList;
		}

		public function getLink() {
			return $this->link;
		}

		public function getParameterList(): array {
			return $this->parameterList;
		}
	}
