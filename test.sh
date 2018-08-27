#!/usr/bin/env php
<?php
	declare(strict_types=1);
	$ports = [
		3306 => [
			'desc' => 'MySQL',
			'host' => 'edde-mysql',
		],
		5432 => [
			'desc' => 'Postgres',
			'host' => 'edde-postgres',
		],
	];
	$attempts = 8;
	$sleep = 1;
	foreach ($ports as $port => $item) {
		printf("Waiting for [%s] on [%s:%s]\n", $item['desc'], $item['host'], $port);
		for ($i = 0; $i < $attempts; $i++) {
			if (($connection = @fsockopen($item['host'], $port)) === false) {
				printf(".");
				sleep($sleep);
				continue;
			}
			fclose($connection);
			printf("\tService available!\n\n");
			continue 2;
		}
		die(sprintf("\nFailed to wait for a service [%s].\n", $item['desc']));
	}
	echo shell_exec('phpunit --coverage-text');
