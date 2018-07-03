<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Job\Inject\JobManager;
	use Edde\Api\Store\Inject\Store;
	use Edde\Api\Thread\Inject\Executor;
	use Edde\Api\Thread\IThreadManager;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object\Object;

	abstract class AbstractThreadManager extends Object implements IThreadManager {
		use Executor;
		use Store;
		use JobManager;
		use ConfigurableTrait;
		/**
		 * @var int
		 */
		protected $maximumThreadCount = 4;

		/**
		 * @inheritdoc
		 */
		public function execute(array $parameterList = null): IThreadManager {
			if ($this->jobManager->hasJob()) {
				$this->executor->execute($parameterList);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function pool(): IThreadManager {
			if ($this->jobManager->hasJob() === false) {
				return $this;
			}
			$this->updateThreadCount(1);
			try {
				$this->jobManager->execute();
				return $this;
			} finally {
				$this->updateThreadCount(-1);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function setMaximumThreadCount(int $maximumThreadCount): IThreadManager {
			$this->maximumThreadCount = $maximumThreadCount;
			return $this;
		}

		protected function updateThreadCount(int $number) {
			$this->store->block($lock = (static::class . '/currentThreadCount'));
			$this->store->set($lock, $this->store->get($lock, 0) + $number);
			$this->store->unlock($lock);
		}
	}
