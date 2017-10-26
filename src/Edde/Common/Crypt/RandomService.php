<?php
	namespace Edde\Common\Crypt;

		use Edde\Api\Crypt\IRandomService;
		use Edde\Common\Object\Object;

		class RandomService extends Object implements IRandomService {
			/**
			 * @inheritdoc
			 */
			public function generate(int $length = 10, string $charList = '0-9a-z'): string {
				$charList = str_shuffle(preg_replace_callback('#.-.#', function ($m) {
					return implode('', range($m[0][0], $m[0][2]));
				}, $charList));
				$charListLength = strlen($charList);
				$string = '';
				$rand0 = null;
				$rand1 = null;
				$rand2 = $this->bytes($length);
				for ($i = 0; $i < $length; $i++) {
					if ($i % 5 === 0) {
						list($rand0, $rand1) = explode(' ', microtime());
						$rand0 += lcg_value();
					}
					$rand0 *= $charListLength;
					$string .= $charList[($rand0 + $rand1 + ord($rand2[$i % strlen($rand2)])) % $charListLength];
					$rand0 -= (int)$rand0;
				}
				return $string;
			}

			/**
			 * @inheritdoc
			 */
			public function bytes(int $length): string {
				return random_bytes($length);
			}

			/**
			 * @inheritdoc
			 */
			public function guid(string $seed = null): string {
				$data = $seed ? substr(hash('sha512', $seed), 0, 16) : $this->bytes(16);
				$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
				$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
				return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
			}
		}