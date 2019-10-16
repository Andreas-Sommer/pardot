.. include:: ../Includes.txt


.. _developer:

================
Developer Corner
================

Target group: **Developers**

This is your opportunity to pass on information to other developers who may be using your extension.

Use this section to provide examples of code or detail any information that would be deemed relevant to a developer.

You may wish to explain how a certain feature was implemented or detail any changes that might of been
made to the extension.

.. _developer-api:

API
===

Initialize PardotService

.. code-block:: php

   $pardotService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
      \Belsignum\Pardot\Service\PardotService::class
   );


Get visitor ID

.. code-block:: php

  $visitorID = $pardotService->getVisitorId();


Use functions of the pardot-api by https://github.com/Cyber-Duck/Pardot-API or my fork https://github.com/belsignum/Pardot-API

.. code-block:: php
  
  $visitor = $pardotService->pardot-><OBJECT>()-><METHOD>

  $visitor = $pardotService->pardot->visitor()->read($visitorID)
