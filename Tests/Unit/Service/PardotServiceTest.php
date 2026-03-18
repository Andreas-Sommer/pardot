<?php

declare(strict_types=1);

namespace Belsignum\Pardot\Tests\Unit\Service;

use Belsignum\Pardot\Service\PardotService;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class PardotServiceTest extends UnitTestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['pardot']);
        parent::tearDown();
    }

    public function testGetExtConfSettingsPrefersRuntimeOverridesFromGlobals(): void
    {
        $extensionConfiguration = $this->createConfiguredMock(
            ExtensionConfiguration::class,
            ['get' => ['account' => 'from-extension-config', 'accessTokenStorage' => '/pardotApi/from-extension-config.json']]
        );
        GeneralUtility::addInstance(ExtensionConfiguration::class, $extensionConfiguration);

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['pardot'] = [
            'account' => 'from-runtime-config',
            'accessTokenStorage' => '/pardotApi/from-runtime-config.json',
            'staticVisitorId' => 'runtime-static-visitor',
        ];

        $subject = new class() extends PardotService {
            public function __construct()
            {
            }

            public function getSettingsForTest(): array
            {
                return $this->settings;
            }
        };
        $subject->getExtConfSettings();

        self::assertSame('from-runtime-config', $subject->getSettingsForTest()['account']);
        self::assertSame('runtime-static-visitor', $subject->getSettingsForTest()['staticVisitorId']);
        self::assertSame(
            Environment::getProjectPath() . '/config/pardotApi/from-runtime-config.json',
            $subject->getSettingsForTest()['accessTokenStorage']
        );
    }

    public function testGetExtConfSettingsKeepsLegacyResolutionForAbsoluteNonPardotApiPaths(): void
    {
        $extensionConfiguration = $this->createConfiguredMock(
            ExtensionConfiguration::class,
            ['get' => ['accessTokenStorage' => '/legacy-token-storage.json']]
        );
        GeneralUtility::addInstance(ExtensionConfiguration::class, $extensionConfiguration);

        $subject = new class() extends PardotService {
            public function __construct()
            {
            }

            public function getSettingsForTest(): array
            {
                return $this->settings;
            }
        };
        $subject->getExtConfSettings();

        self::assertSame(
            Environment::getConfigPath() . '/legacy-token-storage.json',
            $subject->getSettingsForTest()['accessTokenStorage']
        );
    }

    public function testValidateOAuthConfigReturnsFalseIfFileDoesNotExist(): void
    {
        $subject = $this->buildServiceWithSettings([
            'accessTokenStorage' => Environment::getConfigPath() . '/does-not-exist-for-test.json',
        ]);

        self::assertFalse($subject->callValidateOAuthConfigForTest());
    }

    public function testValidateOAuthConfigReturnsTrueForValidJsonFile(): void
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'pardot-config-');
        self::assertNotFalse($temporaryFile);
        file_put_contents($temporaryFile, '{"access_token":"token"}');

        $subject = $this->buildServiceWithSettings([
            'accessTokenStorage' => $temporaryFile,
        ]);

        self::assertTrue($subject->callValidateOAuthConfigForTest());
        unlink($temporaryFile);
    }

    public function testValidateOAuthConfigReturnsFalseForInvalidJsonFile(): void
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'pardot-config-');
        self::assertNotFalse($temporaryFile);
        file_put_contents($temporaryFile, '{invalid-json');

        $subject = $this->buildServiceWithSettings([
            'accessTokenStorage' => $temporaryFile,
        ]);

        self::assertFalse($subject->callValidateOAuthConfigForTest());
        unlink($temporaryFile);
    }

    public function testGetVisitorIdReturnsNullIfCookieMissingAndNoStaticVisitorIdIsSet(): void
    {
        $subject = $this->buildServiceWithSettings([
            'account' => '918823',
            'staticVisitorId' => '',
        ]);

        unset($_COOKIE['visitor_id918823']);
        self::assertNull($subject->getVisitorId());
    }

    public function testGetVisitorIdReturnsStaticVisitorIdIfConfigured(): void
    {
        $subject = $this->buildServiceWithSettings([
            'account' => '918823',
            'staticVisitorId' => 'static-visitor-id',
        ]);

        self::assertSame('static-visitor-id', $subject->getVisitorId());
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function buildServiceWithSettings(array $settings): object
    {
        return new class($settings) extends PardotService {
            /**
             * @param array<string, mixed> $settings
             */
            public function __construct(private array $settingsForTest)
            {
                $this->settings = $settingsForTest;
            }

            public function callValidateOAuthConfigForTest(): bool
            {
                return $this->validateOAuthConfig();
            }
        };
    }
}
