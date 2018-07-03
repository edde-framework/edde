<?php
	declare(strict_types=1);

	namespace Edde\Ext\Database\Sqlite;

	use Edde\Api\Asset\LazyAssetDirectoryTrait;
	use Edde\Common\Database\AbstractDsn;

	/**
	 * Little Johnny was sitting in class doing math problems when his teacher picked him to answer a question, "Johnny, if there were five birds sitting on a fence and you shot one with your gun, how many would be left?"
	 * "None," replied Johnny, "cause the rest would fly away."
	 * "Well, the answer is four," said the teacher, "but I like the way you're thinking."
	 * Little Johnny says, "I have a question for you. If there were three women eating ice cream cones in a shop, one was licking her cone, the second was biting her cone and the third was sucking her cone, which one is married?"
	 * "Well," said the teacher nervously, "I guess the one sucking the cone."
	 * "No," said Little Johnny, "the one with the wedding ring on her finger, but I like the way you're thinking."
	 */
	class SqliteDsn extends AbstractDsn {
		use LazyAssetDirectoryTrait;

		public function __construct(string $filename, array $optionList = []) {
			parent::__construct($filename, $optionList);
		}

		/**
		 * @inheritdoc
		 */
		public function getDsn(): string {
			return 'sqlite:' . $this->assetDirectory->filename($this->dsn);
		}
	}
