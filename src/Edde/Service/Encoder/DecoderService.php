<?php
	declare(strict_types=1);
	namespace Edde\Service\Encoder;

	use Edde\Encoder\IDecoderService;

	trait DecoderService {
		/** @var IDecoderService */
		protected $decoderService;

		/**
		 * @param IDecoderService $decoderService
		 */
		public function injectDecoderService(IDecoderService $decoderService): void {
			$this->decoderService = $decoderService;
		}
	}
