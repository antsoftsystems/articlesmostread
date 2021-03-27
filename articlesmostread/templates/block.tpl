{**
 * plugins/blocks/articlesmostread/block.tpl
 *
 * Copyright (c) 2021 William Costa Rodrigues
 * Copyright (c) 2021 AntSoft Systems On Demand
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * "Articles Most Read" block.
 *}
<div class="pkp_block block_developed_by">
	<div class="content">
		<span class="title">{translate|escape key="plugins.block.ArticlesMostRead.blockTitle"}</span>
			<ul class="articles_most_read">
			{foreach from=$resultMetrics item=article}
				<li class="list_most_read_article">
					<div class="most_read_article_title"><span class="fa fa-file-o"></span> <a href="{url journal=$article.journalPath page="article" op="view" path=$article.articleId}">{$article.articleTitle}{if !empty($article.articleSubTitle)} {$article.articleSubTitle}{/if}</a></div>
					<div class="most_read_article_journal"><span class="fa fa-eye"></span> {$article.metric}</div>
				</li>
			{/foreach}
			</ul>
	</div>
</div>
