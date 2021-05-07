(function($){
	var pageLibs = $(".load-libs");
	$(document).on('click','.t4b-libs__sidebar li',function(e){
		e.preventDefault();
		e.stopPropagation();
		$(".t4b-libs__sidebar li").removeClass('t4b-active');
		$(this).addClass('t4b-active');
	});
	$(document).on('onmouseover onmouseleave','.hasTooltip',function(e){
		e.preventDefault();
		e.stopPropagation();
	});
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

})(jQuery);