<?php
	declare(strict_types=1);
	namespace Edde\Api\Http;

		interface IResponse extends IHttp {
			const R200_OK = 200;
			const R200_OK_CREATED = 201;
			const R200_NO_CONTENT = 204;
			const R400_BAD_REQUEST = 400;
			const R400_NOT_FOUND = 404;
			const R400_NOT_ALLOWED = 405;
			const R500_SERVER_ERROR = 500;
		}
