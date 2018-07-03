<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Common\Html\Document\DocumentControl;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	class DocumentControlTest extends TestCase {
		/**
		 * @var DocumentControl
		 */
		protected $documentControl;

		public function testCommon() {
			$this->documentControl->getHead()
				->setTitle('some meaningfull title');
			$this->documentControl->dirty();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>some meaningfull title</title>
	</head>
	<body></body>
</html>
', $this->documentControl->render());
		}

		public function testDocumentStyleJavascript() {
			$head = $this->documentControl->getHead();
			$head->addJavaScript('/some/javascript/file.js');
			$head->addJavaScript('/another/javascript-file.js');
			$head->addStyleSheet('/style.css');
			$head->addStyleSheet('/stylish-style.css');
			$head->setTitle('some meaningfull title');
			$this->documentControl->dirty();
			self::assertEquals('<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>some meaningfull title</title>
		<script src="/some/javascript/file.js"></script>
		<script src="/another/javascript-file.js"></script>
		<link rel="stylesheet" media="all" href="/style.css">
		<link rel="stylesheet" media="all" href="/stylish-style.css">
	</head>
	<body></body>
</html>
', $this->documentControl->render());
		}

		protected function setUp() {
			$container = ContainerFactory::create();
			$this->documentControl = $container->inject(new DocumentControl());
		}
	}
