<?php
namespace Belsignum\Pardot\Utility;

use Belsignum\Pardot\Service\PardotService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ExtensionManagerConfigurationUtility {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/** @var TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility */
	protected $configurationUtility;

	/** @var array */
	protected $extConf = [];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
		$this->configurationUtility = $this->objectManager->get(ConfigurationUtility::class);
		$this->extConf = $this->configurationUtility->getCurrentConfiguration(PardotService::EXTKEY);
	}

	/**
	 * @return string
	 */
	public function passwordField()
	{
		return '<input type="password" id="em-password" name="tx_extensionmanager_tools_extensionmanagerextensionmanager[config][password][value]" class="form-control" value="' . $this->extConf['password']['value'] . '" >';
	}
}
