<?php

declare(strict_types=1);

use TYPO3\TestingFramework\Core\SystemEnvironmentBuilder;
use TYPO3\TestingFramework\Core\Testbase;

(static function (): void {
    $testbase = new Testbase();

    if (!getenv('TYPO3_PATH_ROOT')) {
        putenv('TYPO3_PATH_ROOT=' . rtrim($testbase->getWebRoot(), '/'));
    }
    if (!getenv('TYPO3_PATH_WEB')) {
        putenv('TYPO3_PATH_WEB=' . rtrim($testbase->getWebRoot(), '/'));
    }

    $testbase->defineSitePath();

    $composerMode = defined('TYPO3_COMPOSER_MODE') && TYPO3_COMPOSER_MODE === true;
    $requestType = SystemEnvironmentBuilder::REQUESTTYPE_BE | SystemEnvironmentBuilder::REQUESTTYPE_CLI;
    SystemEnvironmentBuilder::run(0, $requestType, $composerMode);

    $testbase->createDirectory('typo3temp/var/tests');
    $testbase->createDirectory('typo3temp/var/transient');
})();
