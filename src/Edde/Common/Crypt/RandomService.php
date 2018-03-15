<?php
	declare(strict_types=1);
	namespace Edde\Common\Crypt;

	use Edde\Api\Crypt\IRandomService;
	use Edde\Common\Object\Object;

	class RandomService extends Object implements IRandomService {
		/** @inheritdoc */
		public function generate(int $length = 10, string $chars = '0-9a-z'): string {
			$chars = str_shuffle(preg_replace_callback('#.-.#', function ($m) {
				return implode('', range($m[0][0], $m[0][2]));
			}, $chars));
			$len = strlen($chars);
			$string = '';
			$rand0 = null;
			$rand1 = null;
			$rand2 = $this->bytes($length);
			for ($i = 0; $i < $length; $i++) {
				if ($i % 5 === 0) {
					[$rand0, $rand1] = explode(' ', microtime());
					$rand0 += lcg_value();
				}
				$rand0 *= $len;
				$string .= $chars[($rand0 + $rand1 + ord($rand2[$i % strlen($rand2)])) % $len];
				$rand0 -= (int)$rand0;
			}
			return $string;
		}

		/** @inheritdoc */
		public function bytes(int $length): string {
			return random_bytes($length);
		}

		/** @inheritdoc */
		public function uuid(string $seed = null): string {
			$seed = $seed ? substr(hash('sha512', $seed), 0, 16) : $this->bytes(16);
			$seed[6] = chr(ord($seed[6]) & 0x0f | 0x40);
			$seed[8] = chr(ord($seed[8]) & 0x3f | 0x80);
			return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($seed), 4));
		}
	}
