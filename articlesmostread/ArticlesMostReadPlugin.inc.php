<?php

/**
 * @file plugins/blocks/AllMostRead/AllMostRead.inc.php
 *
 * Copyright (c) 2021-2022 William Costa Rodrigues
 * Copyright (c) 2021-2022 AntSoft Systems On Demand
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class AllMostRead
 * @ingroup plugins_blocks_AllMostRead
 *
 * @brief Class for "All Most Read" block plugin, based on Plugin mostRead
 */

import('lib.pkp.classes.plugins.BlockPlugin');

class ArticlesMostReadPlugin extends BlockPlugin {

	/**
	 * Install default settings on journal , in sidebar. Get the default setting.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Get the display name of this plugin to OJS.
	 * @return String
	 */
	function getDisplayName() {
		return __('plugins.block.ArticlesMostRead.displayName');
	}

	/**
	 * Get a description of the plugin.
	 */
	function getDescription() {
		return __('plugins.block.ArticlesMostRead.description');
	}
/**
	 * @copydoc Plugin::getActions()
	 */
	function getActions($request, $actionArgs) {
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
			$this->getEnabled()?array(
				new LinkAction(
					'settings',
					new AjaxModal(
						$router->url($request, null, null, 'manage', null, array_merge($actionArgs, array('verb' => 'settings'))),
						$this->getDisplayName()
					),
					__('manager.plugins.settings'),
					null
				),
			):array(),
			parent::getActions($request, $actionArgs)
		);
	}
	/**
	 * @copydoc Plugin::manage()
	 */
	function manage($args, $request) {
		$this->import('MostAllReadSettingsForm');
		$context = Application::getRequest()->getContext();
		$contextId = ($context && isset($context) && $context->getId()) ? $context->getId() : CONTEXT_SITE;
		switch($request->getUserVar('verb')) {
			case 'settings':
				$settingsForm = new MostReadSettingsForm($this, $contextId);
				$settingsForm->initData();
				return new JSONMessage(true, $settingsForm->fetch($request));
			case 'save':
				$settingsForm = new MostReadSettingsForm($this, $contextId);
				$settingsForm->readInputData();
				if ($settingsForm->validate()) {
					$settingsForm->execute();
					$notificationManager = new NotificationManager();
					$notificationManager->createTrivialNotification(
						$request->getUser()->getId(),
						NOTIFICATION_TYPE_SUCCESS,
						array('contents' => __('plugins.blocks.mostRead.settings.saved'))
					);
					return new JSONMessage(true);
				}
				return new JSONMessage(true, $settingsForm->fetch($request));
		}
		return parent::manage($args, $request);
	}

	/**
	 * @see BlockPlugin::getContents
	 */
	function getContents($templateMgr, $request = null) {
		$context = $request->getContext();
		if (!$context) return '';

		$metricsDao = DAORegistry::getDAO('MetricsDAO');
		$cacheManager =& CacheManager::getManager();
		$cache  =& $cacheManager->getCache('allmostread', $context->getId(), array($this, '_cacheMissAllMostRead'));
		$daysToStale = 1;
		$cachedMetrics = false;

		if (time() - $cache->getCacheTime() > 60 * 60 * 24 * $daysToStale) {
			$cachedMetrics = $cache->getContents();
			$cache->flush();
		}
		
		$resultMetrics = $cache->getContents();

		if (!$resultMetrics && $cachedMetrics) {
			$resultMetrics = $cachedMetrics;
			$cache->setEntireCache($cachedMetrics);
		} elseif (!$resultMetrics) {
			$cache->flush();
		}

		$templateMgr->assign('resultMetrics', $resultMetrics);
		return parent::getContents($templateMgr, $request);
	}


	function _cacheMissAllMostRead($cache) {
			$metricsDao = DAORegistry::getDAO('MetricsDAO');
			$publishedArticleDao = DAORegistry::getDAO('PublishedArticleDAO');
			$journalDao = DAORegistry::getDAO('JournalDAO');
			$request = Application::getRequest();
			$context = $request->getContext();
			$result = $metricsDao->retrieve(
				"SELECT submission_id, SUM(metric) AS metric FROM metrics WHERE (assoc_type='515' AND submission_id IS NOT NULL) AND (context_id='?') GROUP BY submission_id ORDER BY metric DESC LIMIT 10", (int) $context->getId()
			);

			while (!$result->EOF) {
				$resultRow = $result->GetRowAssoc(false);
				$article = $publishedArticleDao->getById($resultRow['submission_id']);	
				$journal = $journalDao->getById($article->getJournalId());
				$articles[$resultRow['submission_id']]['journalPath'] = $journal->getPath();
				$articles[$resultRow['submission_id']]['articleId'] = $article->getBestArticleId();
				$articles[$resultRow['submission_id']]['articleTitle'] = $article->getLocalizedTitle();
				$articles[$resultRow['submission_id']]['articleSubTitle'] = $article->getLocalizedSubtitle();
				$articles[$resultRow['submission_id']]['metric'] = $resultRow['metric'];
				$result->MoveNext();
			}
			$result->Close();			
			$cache->setEntireCache($articles);
			return $result;
	}

}

?>
