<?php
namespace Belsignum\Pardot\Service;
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 15.10.2019
 * Time: 16:29
 */

use CyberDuck\Pardot\PardotApi;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PardotService
{
    /**
     * Extension key
     */
    public const EXTKEY = 'pardot';

    /**
     * @var PardotApi|null
     */
    public ?PardotApi $pardot;

    /**
     * Extension Settings
     *
     * @var array
     */
    protected array $settings;

    /**
     * initialize API
     */
    public function __construct()
    {
        $this->getExtConfSettings();

        if($this->validateOAuthConfig() === false)
        {
            $this->pardot = null;
            return;
        }

        $this->pardot = GeneralUtility::makeInstance(
            PardotApi::class,
            $this->settings['clientId'],
            $this->settings['clientSecret'],
            $this->settings['redirectUri'],
            $this->settings['businessUnitId'],
            $this->settings['accessTokenStorage']
        );
        $debug = (bool) $this->settings['debug'];
        $this->pardot->setDebug($debug);
    }

    /**
     * get extension configuration settings
     * @return void
     */
    public function getExtConfSettings()
    {
        $this->settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get(self::EXTKEY);

        $this->settings['accessTokenStorage'] = Environment::getConfigPath() . $this->settings['accessTokenStorage'];
    }

    /**
     * get the visitor ID set by cookie
     * @return string
     */
    public function getVisitorId()
    {
        if ($this->settings['staticVisitorId'])
        {
            return $this->settings['staticVisitorId'];
        }
        return $_COOKIE['visitor_id' . $this->settings['account']];
    }

    protected function validateOAuthConfig(): bool
    {
        if(file_exists($this->settings['accessTokenStorage']))
        {
            $configJson = file_get_contents($this->settings['accessTokenStorage']);
            if (version_compare(phpversion(), '8.3.0', '<'))
            {
                json_decode($configJson);
                return json_last_error() === JSON_ERROR_NONE;
            }
            return json_validate($configJson);
        }
        return false;
    }
}
