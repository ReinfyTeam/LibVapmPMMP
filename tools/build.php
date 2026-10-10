<?php

/*
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * LibVapmPMMP provides asynchronous, promise, coroutine and thread utilities
 * for PocketMine-MP.
 * Original authors: VennDev, xqwtxon and xwertxy.
 * Maintained by ReinfyTeam.
 * @link https://github.com/ReinfyTeam/LibVapmPMMP
 *
 *
 */

declare(strict_types=1);

const COMPRESS_FILES = true;
const COMPRESSION = Phar::GZ;

$from = getcwd() . DIRECTORY_SEPARATOR;
$to = getcwd() . DIRECTORY_SEPARATOR . "build" . DIRECTORY_SEPARATOR;

@mkdir($to, 0777, true);

copyDirectory($from . "src", $to . "src");

$pluginYml = yaml_parse_file($from . "plugin.yml");

yaml_emit_file($to . "plugin.yml", (array) $pluginYml);

$outputPath = getcwd() . DIRECTORY_SEPARATOR . $pluginYml["name"] . ".phar";

@unlink($outputPath);

$phar = new Phar($outputPath);
$phar->buildFromDirectory($to);

if (COMPRESS_FILES) $phar->compressFiles(COMPRESSION);

removeDirectory($to);

print("Succeed! Output path: $outputPath");

function copyDirectory(string $from, string $to) : void{
	@mkdir($to, 0777, true);
	$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
	foreach($files as $fileInfo){
		$target = str_replace($from, $to, $fileInfo->getPathname());
		if($fileInfo->isDir()) @mkdir($target, 0777, true);
		else{
			$contents = file_get_contents($fileInfo->getPathname());
			file_put_contents($target, $contents);
		}
	}
}

function removeDirectory(string $dir) : void{
	$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
	foreach($files as $fileInfo){
		if($fileInfo->isDir()) rmdir($fileInfo->getPathname());
		else unlink($fileInfo->getPathname());
	}
	rmdir($dir);
}
