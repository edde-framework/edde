#!/usr/bin/env php
<?php
	declare(strict_types = 1);

	if (class_exists('Phar') === false || ini_get('phar.readonly')) {
		echo "Enable Phar extension and set directive 'phar.readonly=off'.\n";
		exit(1);
	}

	$name = json_decode(file_get_contents(__DIR__ . '/../composer.json'));
	/** @noinspection PhpUsageOfSilenceOperatorInspection */
	@unlink($file = (__DIR__ . '/../release/' . str_replace('edde-framework/', '', $name->name) . '.phar'));
	/** @noinspection MkdirRaceConditionInspection */
	/** @noinspection PhpUsageOfSilenceOperatorInspection */
	@mkdir(dirname($file));

	$stub = '<?php Phar::mapPhar("{phar-file}"); require_once("phar://{phar-file}/loader.php"); __HALT_COMPILER();';

	$phar = new \Phar($file, 0, $pharFile = basename($file));
	$phar->setStub(str_replace('{phar-file}', $pharFile, $stub));
	$phar->buildFromDirectory(__DIR__ . '/../src');
	$phar->compressFiles(\Phar::GZ);

	exit(0);
