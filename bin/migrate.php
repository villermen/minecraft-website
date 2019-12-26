#!/usr/bin/env php
<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$container = \Villermen\Minecraft\App::createContainer();
/** @var \Villermen\Minecraft\Migration\Migrator $migrator */
$migrator = $container->get(\Villermen\Minecraft\Migration\Migrator::class);
$migrator->migrate();

