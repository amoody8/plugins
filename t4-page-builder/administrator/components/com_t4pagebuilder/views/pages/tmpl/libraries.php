<?php

/** 
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 * @forum:		  https://www.joomlart.com/forums/t/t4-builder
 * @Link:         https://demo.t4-builder.joomlart.com/
 *------------------------------------------------------------------------------
 */
// check if need separated layout for Joomla 3!
if (($j3 = \JPB\Helper\Layout::j3(__FILE__))) {
	include $j3;
	return;
}
defined('_JEXEC') or die;
 
$app       = JFactory::getApplication();
$doc 	   = JFactory::getDocument();
$user      = JFactory::getUser();
$doc->addScript( JPB_PATH_BASE . "assets/js/library.j4.js");
$userId    = $user->get('id');
$catCount = 0;
$countItem = 0;
$base_url = JUri::root(true);
$doc->addScriptDeclaration('
	Joomla.submitbuttonurl = function()
	{
		var form = document.getElementById("adminForm");

		// do field validation 
		if (form.install_url.value == "" || form.install_url.value == "http://" || form.install_url.value == "https://") {
			alert("' . JText::_('PLG_INSTALLER_URLINSTALLER_NO_URL', true) . '");
		}
		else
		{
			JoomlaInstaller.showLoading();
			form.installtype.value = "url"
			form.submit();
		}
	};
');

JFactory::getDocument()->addStyleDeclaration(
	'
	#loading {
		background: rgba(255, 255, 255, .8) url(\'' . JHtml::_('image', 'jui/ajax-loader.gif', '', null, true, true) . '\') 50% 15% no-repeat;
		position: fixed;
		opacity: 0.8;
		-ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity = 80);
		filter: alpha(opacity = 80);
		overflow: hidden;
	}
	'
);
?>

<div class="load-libs" data-baseurl="<?php echo JUri::root(true);?>">
  	<div class="container-fluid t4b-container">
    	<page></page>
	</div>
</div>

<script src="<?php echo JPB_PATH_BASE;?>views/pages/tmpl/page.riot?r=<?php echo rand(0,1000) ?>" type="riot"></script>
<script src="<?php echo \JUri::root(true). JPB_MEDIA_BUILDER ;?>vendors/riot/riot+compiler.min.js"></script>
