<?php
	declare(strict_types=1);
	namespace Edde\Service\Encoder;

	use Edde\Encoder\IEncoderService;

	trait EncoderService {
		/** @var IEncoderService */
		protected $encoderService;

		/**
		 * @param IEncoderService $encoderService
		 */
		public function injectEncoderService(IEncoderService $encoderService): void {
			$this->encoderService = $encoderService;
		}
	}
