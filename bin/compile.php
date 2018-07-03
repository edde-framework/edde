#!/usr/bin/env php
<?php
	declare(strict_types=1);

	use Edde\Common\File\FileUtils;
	use Edde\Framework;

	require_once __DIR__ . '/../loader.php';

	if (class_exists('Phar') === false || ini_get('phar.readonly')) {
		echo "Enable Phar extension and set directive 'phar.readonly=off'.\n";
		exit(1);
	}

	$name = 'edde-framework';
	$version = $name . '-' . (new Framework())->getVersion();

	$rootDir = realpath(__DIR__ . '/..');
	$releaseDir = $rootDir . '/release';
	$tempDir = $rootDir . '/temp';
	$pharDir = $tempDir . '/phar';
	$bundleDir = $tempDir . '/bundle';
	$sourceDir = $rootDir . '/src';
	$libDir = $rootDir . '/lib';

	FileUtils::createDir($releaseDir);

	FileUtils::delete($pharDir);
	FileUtils::delete($bundleDir);
	FileUtils::copy($sourceDir, $pharDir . '/src');
	FileUtils::copy($sourceDir, $bundleDir . '/src');
	FileUtils::copy($libDir, $bundleDir . '/lib');
	FileUtils::copy($rootDir . '/loader.php', $bundleDir . '/loader.php');

	function make(string $file, string $source, string $stub) {
		$phar = new \Phar($file, 0, $pharFile = basename($file));
		$phar->setStub(str_replace('{phar-file}', $pharFile, $stub));
		$phar->buildFromDirectory($source);
		$phar->compressFiles(\Phar::GZ);
	}

	make($versionPhar = ($releaseDir . '/' . $version . '.phar'), $pharDir, '<?php Phar::mapPhar("{phar-file}"); require_once("phar://{phar-file}/src/loader.php"); __HALT_COMPILER();');
	FileUtils::copy($versionPhar, $releaseDir . '/' . $name . '.phar');
	make($versionPhar = ($releaseDir . '/' . $version . '.bundle.phar'), $bundleDir, '<?php Phar::mapPhar("{phar-file}"); require_once("phar://{phar-file}/loader.php"); __HALT_COMPILER();');
	FileUtils::copy($versionPhar, $releaseDir . '/' . $name . '.bundle.phar');

	exit(0);
