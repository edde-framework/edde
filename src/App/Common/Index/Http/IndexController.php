<?php
	declare(strict_types=1);
	namespace App\Common\Index\Http;

		use App\Common\Index\AbstractIndexController;
		use Edde\Ext\Control\HttpController;

		class IndexController extends AbstractIndexController {
			use HttpController;

			public function actionIndex() {
				$this->html('<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Edde Framework</title>
		<style>
			body {
				position: relative;
				overflow: hidden;
			}

			.hello-world {
				position: relative;
				top: -100px;
				height: 100vh;
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 80px;
				color: #333;
			}
		</style>
	</head>
	<body>
		<div class="hello-world">This is a beautiful response from a http view!</div>
	</body>
</html>');
			}
		}
