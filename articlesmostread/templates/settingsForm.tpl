{**
 * plugins/blocks/articlesmostRead/settingsForm.tpl
 *
 * Copyright (c) 2022 William Costa Rodrigues
 * Copyright (c) 2022 Antsoft Systems On Demand
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * All articles Most read plugin settings
 *
 *}
<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#mostAllReadSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="mostAllReadSettingsForm" method="post" action="{url op="manage" category="blocks" plugin=$pluginName verb="save"}">
	{csrf}

	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="mostReadFormNotification"}

	{fbvFormArea id="mostAllReadDisplayOptions" title="plugins.blocks.mostAllRead.settings.title"}

		{fbvFormSection for="mostAllReadDays"}
			{fbvElement type="text" label="plugins.blocks.mostRead.settings.days" id="mostAllReadDays" value=$mostReadDays}
		{/fbvFormSection}

		{fbvFormSection for="mostAllReadBlockTitle"}
			{fbvElement type="text" label="plugins.blocks.mostAllRead.settings.blockTitle" id="mostAllReadBlockTitle" value=$mostAllReadBlockTitle multilingual=true}
		{/fbvFormSection}		

	{/fbvFormArea}

	{fbvFormButtons id="WGLSettingsFormSubmit" submitText="common.save" hideCancel=true}

</form>
