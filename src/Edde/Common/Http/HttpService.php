<?php
	declare(strict_types=1);
	namespace Edde\Common\Http;

		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Http\IHttpService;
		use Edde\Api\Http\IRequest;
		use Edde\Api\Http\IResponse;
		use Edde\Common\Object\Object;
		use Edde\Common\Url\Url;

		class HttpService extends Object implements IHttpService {
			use Container;
			/**
			 * @var IRequest
			 */
			protected $request;
			/**
			 * @var IResponse
			 */
			protected $response;

			/**
			 * @inheritdoc
			 */
			public function getRequest(): IRequest {
				if ($this->request) {
					return $this->request;
				}
				$this->request = new Request(Url::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), $this->createHeaders(), $this->createCookies());
				$input = fopen('php://input', 'rb');
				$content = null;
				if (empty($_POST) === false) {
					$content = new Content($_POST, 'post');
				} else if (fgetc($input) !== false) {
					$headers = $this->request->getHeaders();
					$contentType = $headers->getContentType();
					$content = new Content('php://input', 'stream+' . $contentType->getMime());
				}
				fclose($input);
				$this->request->setContent($content);
				return $this->request;
			}

			/**
			 * @inheritdoc
			 */
			public function getResponse(): IResponse {
				if ($this->response) {
					return $this->response;
				}
				return $this->response = new Response(200, $this->container->create(Headers::class, [], __METHOD__), $this->container->create(Cookies::class, [], __METHOD__));
			}

			/**
			 * @inheritdoc
			 */
			public function setResponse(IResponse $response): IHttpService {
				$this->response = $response;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function send(): IHttpService {
				$response = $this->getResponse();
				$response->send();
				return $this;
			}
		}
