<?php
	declare(strict_types=1);
	namespace Edde\Tick;

	use Edde\Edde;

	class TickService extends Edde implements ITickService {
		/** @inheritdoc */
		public function tick(): ITickService {
			return $this;
		}
	}
