<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Http\Client\LazyHttpClientTrait;
	use Edde\Api\Thread\IExecutor;
	use Edde\Api\Url\IUrl;
	use Edde\Api\Url\UrlException;
	use Edde\Common\Http\RequestUrl;

	class WebExecutor extends AbstractExecutor {
		use LazyHttpClientTrait;
		/**
		 * @var IUrl
		 */
		protected $url;

		/**
		 * @param string|IUrl $url
		 *
		 * @return IExecutor
		 * @throws UrlException
		 */
		public function setUrl($url): IExecutor {
			$this->url = RequestUrl::create($url);
			$scheme = $this->url->getScheme();
			$this->url->setScheme('tcp');
			if ($scheme === 'https' || $scheme === 'ssl') {
				$this->url->setScheme('ssl');
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(array $parameterList = null): IExecutor {
			$url = $this->url;
			if ($parameterList) {
				$url = clone $url;
				$url->addParameterList($parameterList);
			}
			$this->httpClient->touch($url);
			return $this;
		}
	}
