<?php
namespace Belsignum\Pardot\Service;
/**
 * Created by PhpStorm.
 * User: Andreas Sommer
 * Date: 15.10.2019
 * Time: 16:29
 */

use CyberDuck\Pardot\PardotApi;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

class PardotService
{

	/**
	 * Extension key
	 */
	public const EXTKEY = 'pardot';

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

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
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManager $objectManager)
	{
		$this->objectManager = $objectManager;
	}

	/**
	 * initialize API
	 */
	public function __construct()
	{
		$this->injectObjectManager(new ObjectManager());
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
		$configurationUtility = $this->objectManager->get(ConfigurationUtility::class);
		$extensionConfiguration = $configurationUtility->getCurrentConfiguration(self::EXTKEY);
		if(\is_array($extensionConfiguration))
		{
			foreach ($extensionConfiguration as $key => $config)
			{
				$this->settings[$key] = $config['value'];
			}
		}
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
