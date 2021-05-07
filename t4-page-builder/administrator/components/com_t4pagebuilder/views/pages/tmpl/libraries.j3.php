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
defined('_JEXEC') or die;
 
header("Access-Control-Allow-Origin: *");
JHtml::_('behavior.core');
JHtml::_('behavior.formvalidator');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$app       = JFactory::getApplication();
$doc 	   = JFactory::getDocument();
$user      = JFactory::getUser();
$userId    = $user->get('id');
$doc->addScript( JPB_PATH_BASE . "assets/js/library.js");
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
<script>
jQuery(document).ready(function($){
	// set first tab active
	var tabAnchor = $("#importTabTabs li:first a");
	tabAnchor.click();
});
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
	
postData(remote_url+'member/builderbundle/get-all')
.then((data) => {
		data = rebuildData(data.data);
	// JSON data parsed by `response.json()` call
	var baseUrl = "<?php echo $base_url;?>";
	var url_base = "<?php echo \JUri::base();?>";
	var dataConfig = JSON.parse(localStorage.getItem('t4bConfig')),loginStatus = dataConfig.login_status;
	localStorage.setItem('t4bpage',JSON.stringify(data));
	var users = {};
	if(loginStatus){
		users.user_id = dataConfig.user_id;
		users.name = dataConfig.name;
		users.username = dataConfig.username;
		users.username = dataConfig.username;
		users.email = dataConfig.email;
		var date_ex = "";
		if(dataConfig.expire_date){
			var date = new Date(dataConfig.expire_date);
			const months = ["JAN", "FEB", "MAR","APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
			date_ex = months[date.getMonth()] + ' ' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + ', ' + date.getFullYear();
		}
		users.expire_date = date_ex;
		users.user_type = (dataConfig.user_type) ? dataConfig.user_type : "Free";
		var btnEv = "";
        if(dataConfig.user_token && users.user_type == "Free"){
            
            btnEv = "buildpro";
        }
	}
	(async function main() {
  		await riot.compile()
	 	riot.mount('page',{
		  title: 'Page Libraries',
		  baseUrl: baseUrl,
		  btnEv:btnEv,
		  users:users,
		  loginStatus: loginStatus,
		  items: data
		})
	}())
});
var rebuildData = function (data){
	data.sort((a,b) => parseFloat(b.id) - parseFloat(a.id)).forEach(item => {
		if(typeof item.screenshots == "object" && item.screenshots != ""){
			item.thumb = item.screenshots[0]['url'];
		}else{
			item.thumb = "";
		}
		
	})
	return data;
};

</script>