<?php

/**
 * @defgroup plugins_blocks_ArticlesMostRead block plugin
 */

/**
 * @file plugins/blocks/AllMostRead/index.php
 *
 * Copyright (c) 2021 William Costa Rodrigues
 * Copyright (c) 2021 AntSoft Systems On Demand
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_blocks_ArticlesMostReadPlugin
 * @brief Wrapper for "Articles Most Read" block plugin.
 *
 */

require_once('ArticlesMostReadPlugin.inc.php');

return new ArticlesMostReadPlugin();

?>
