<?php
	namespace Edde\Common\Iterator;

		class StringIterator implements \IteratorAggregate {
			/**
			 * @var string
			 */
			protected $string;

			public function __construct(string $string) {
				$this->string = $string;
			}

			public function getIterator() {
				$length = mb_strlen($string = $this->string);
				while ($length) {
					yield mb_substr($string, 0, 1);
					$string = mb_substr($string, 1, $length);
					$length = mb_strlen($string);
				}
			}
		}
