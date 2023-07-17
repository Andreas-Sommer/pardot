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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PardotService
{

	/**
	 * Extension key
	 */
	public const EXTKEY = 'pardot';

	/**
	 * @var PardotApi
	 */
	public $pardot;

	/**
	 * Extension Settings
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * initialize API
	 */
	public function __construct()
	{
		$this->getExtConfSettings();

		$this->pardot = GeneralUtility::makeInstance(
			PardotApi::class,
			$this->settings['email'],
			$this->settings['password'],
			$this->settings['user_key']
		);
	}

	/**
	 * get extension configuration settings
	 * @return void
	 */
	public function getExtConfSettings()
	{
		$this->settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)
			->get('pardot');
	}

	/**
	 * get the visitor ID set by cookie
	 * @return string
	 */
	public function getVisitorId()
	{
		return $_COOKIE['visitor_id' . $this->settings['account']];
	}
}
