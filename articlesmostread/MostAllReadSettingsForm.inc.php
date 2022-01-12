<?php

/**
 * @file plugins/blocks/mostRead/MostReadSettingsForm.inc.php
 *
 * Copyright (c) 2022 William Costa Rodrigues
 * Copyright (c) 2022 AntSoft Systems On Demand
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class MostReadSettingsForm
 * @ingroup plugins_generic_mostRead
 *
 * @brief Form for journal managers to modify Most Read plugin settings
 */

import('lib.pkp.classes.form.Form');

class MostAllReadSettingsForm extends Form {

	/**
	 * MostReadSettingsForm constructor.
	 * @param $plugin
	 */
	function __construct($plugin, $contextId){
		$this->plugin = $plugin;
		$this->setContextId($contextId);
		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
		$this->setData('pluginName', $plugin->getName());

		$this->addCheck(new FormValidator($this, 'mostAllReadDays', 'required', 'plugins.blocks.mostAllRead.settings.mostAllReadDaysRequired'));

		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));		
	}
	/**
	 * Get the Context ID.
	 * @return int
	 */
	public function getContextId() {
		return $this->_contextId;
	}

	/**
	 * Set the Context ID.
	 * @param $contextId int
	 */
	public function setContextId($contextId) {
		$this->_contextId = $contextId;
	}
	/**
	 * Initialize form data.
	 */
	function initData(){
		$plugin = $this->plugin;
		$contextId = $this->getContextId();
		$mostReadBlockTitle = unserialize($plugin->getSetting($contextId, 'mostAllReadBlockTitle'));
		$this->setData('mostReadDays', $plugin->getSetting($contextId, 'mostAllReadDays'));
		$this->setData('mostReadBlockTitle', $mostAllReadBlockTitle);
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData(){
		$this->readUserVars(array('mostAllReadDays', 'mostAllReadBlockTitle'));
	}

	/**
	 * @copydoc Form::fetch()
	 */
	function fetch($request, $template = null, $display = false) {
		return parent::fetch($request);
	}

	/**
	 * Save settings.
	 */
	
	public function execute(...$functionArgs) {
		$plugin = $this->plugin;
		$contextId = $this->getContextId();
		$mostAllReadBlockTitle = serialize($this->getData('mostAllReadBlockTitle'));

		$plugin->updateSetting($contextId, 'mostAllReadDays', $this->getData('mostAllReadDays'), 'string');
		$plugin->updateSetting($contextId, 'mostAllReadBlockTitle', $mostAllReadBlockTitle, 'string');

		# empty current cache
		$cacheManager = CacheManager::getManager();
		$cache = $cacheManager->getCache('mostallread', $contextId, array($plugin, '_cacheMiss'));
		$cache->flush();
		parent::execute(...$functionArgs);
		
	}
}
