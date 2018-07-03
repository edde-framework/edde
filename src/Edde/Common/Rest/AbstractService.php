<?php
	declare(strict_types=1);

	namespace Edde\Common\Rest;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Http\IResponse as IHttpResponse;
	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Rest\IService;
	use Edde\Api\Rest\RestException;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;
	use Edde\Ext\Application\StringContent;

	abstract class AbstractService extends AbstractControl implements IService {
		use LazyResponseManagerTrait;
		use LazyHttpResponseTrait;
		use LazyHostUrlTrait;
		use LazyHttpRequestTrait;
		protected static $methodList = [
			'GET',
			'POST',
			'PUT',
			'PATCH',
			'DELETE',
			'HEAD',
		];

		/**
		 * @inheritdoc
		 */
		public function link($generate, array $parameterList = []) {
			$requestUrl = $this->httpRequest->getRequestUrl();
			$url = Url::create($this->hostUrl->getAbsoluteUrl());
			$url->setPath($generate);
			$parameterList = array_merge($requestUrl->getParameterList(), $parameterList);
			unset($parameterList['action']);
			$url->setParameterList($parameterList);
			return $url->getAbsoluteUrl();
		}

		/**
		 * @inheritdoc
		 */
		public function getMethodList(): array {
			$methodList = [];
			foreach (self::$methodList as $name) {
				if (method_exists($this, $method = ('action' . StringUtils::firstUpper(strtolower($name))))) {
					$methodList[$name] = $method;
				}
			}
			return $methodList;
		}

		protected function error(int $code, string $message) {
			$this->httpResponse->header('Date', gmdate('D, d M Y H:i:s T'));
			return $this->response(new StringContent($message), $code);
		}

		protected function response(IContent $content, int $code = null) {
			$code ? $this->httpResponse->setCode($code) : null;
			$this->responseManager->response($content);
		}

		public function __call(string $name, $parameterList) {
			if (count($parameterList) !== 1) {
				throw new RestException(sprintf('Calling unknown method [%s].', $name));
			}
			list($request) = $parameterList;
			if ($request instanceof IElement === false) {
				throw new RestException(sprintf('Unsupported parameter type [%s].', gettype($request)));
			}
			$methodList = $this->getMethodList();
			if (in_array($name = strtoupper($name), self::$methodList, true) === false) {
				$this->httpResponse->header('Allowed', $allowed = implode(', ', array_keys($methodList)));
				return $this->error(IHttpResponse::R400_NOT_ALLOWED, sprintf('The requested method [%s] is not supported; %s.', str_replace('ACTION', '', $name), empty($methodList) ? 'there are no supported methods' : 'available methods are [' . $allowed . ']'));
			}
			if (isset($methodList[$name]) === false) {
				$this->httpResponse->header('Allowed', $allowed = implode(', ', array_keys($methodList)));
				return $this->error(IHttpResponse::R400_NOT_ALLOWED, sprintf('The requested method [%s] is not implemented; %s.', str_replace('ACTION', '', $name), empty($methodList) ? 'there are no available methods' : 'available methods are [' . $allowed . ']'));
			}
			return $this->{$methodList[$name]}($request);
		}
	}
