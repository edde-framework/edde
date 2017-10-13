<?php
	namespace Edde\Common\Http;

		use Edde\Api\Content\IContent;
		use Edde\Api\Http\ICookies;
		use Edde\Api\Http\IHeaders;
		use Edde\Api\Http\IResponse;

		class Response extends AbstractHttp implements IResponse {
			public function __construct(IContent $content, IHeaders $headers, ICookies $cookies) {
				parent::__construct($headers, $cookies);
				$this->content = $content;
			}
		}
