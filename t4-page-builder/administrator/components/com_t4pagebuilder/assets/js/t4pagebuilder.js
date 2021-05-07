//init t4bConfig
var userConfig = localStorage.getItem('t4bConfig') || "";
var remote_url =  "https://www.joomlart.com/";
// var remote_url =  "http://www.ja.dev.joomlart.com/";
if(!userConfig) localStorage.setItem('t4bConfig',JSON.stringify({'login_status':false,'remote_url':remote_url}));
userConfig = localStorage.getItem('t4bConfig');
userConfig.remote_url = remote_url;
jQuery(document).ready(function($){
    
    //preview 
    $('.t4b-preview-alt').on('click',function(e){
        e.preventDefault();
        Joomla.removeMessages();
        var pageid = $(this).data('pageid');
        var pageUrl = $(this).data('url');
        var pageTitle = $(this).data('pagetitle');
        var previewModal = $('#preview');
        previewModal.find('iframe').attr('src',pageUrl);
        previewModal.find('.modal-header').remove();
        previewModal.find('iframe').addClass('view-desktop');
        if(pageTitle) previewModal.find('.t4b-preview-page-title').html('<i class="fal fa-file-alt"></i>'+pageTitle);
        previewModal.data('view',true);
        previewModal.modal();
    });
    $('.t4b-edit-inline').on('click',function(){
        localStorage.setItem("pageid",$(this).data('pageid'));
        localStorage.setItem("editpage",true);
        localStorage.setItem("editinline",true);
    });
    $('.t4b-preview-devices li').on('click',function(e){
        $('.t4b-preview-devices li').removeClass('active');
        $(this).addClass('active');
        $sizeScreen = $(this).data('device');
        $('.t4b-prewview iframe').attr('class','preview-iframe view-'+$sizeScreen);
    });
    $('#preview').on('click','button.close',function(){
        $('#preview').find('iframe').contents().find("body").html("");
    });
    $i = 0;
    $(".modal").on('hidden.bs.modal',function(e){
        if($(this).data("act") == "cancel") return;
        if(['preview','imageModal_jpb-field-pagetext_media'].indexOf($(this).attr('id')) < 0){
            setTimeout(function(){
                window.parent.location.reload();
            },1000);
        }

    });
    // set first tab active
    var tabAnchor = $("#importTabTabs li:first a");
    tabAnchor.click();
    $('.filter-select').find('select').each(function(){
        $(this).chosen({width:'100%'});
    });

    //update sidebar
     var sidebar = $('#jpb-sidebar'),login = sidebar.find('#t4b-login-form');
     var t4bConfig = JSON.parse(localStorage.getItem('t4bConfig')),
        login_st = t4bConfig.login_status;
    
    if(!login_st){
         var loginform = `<div id="t4b-login-form" class="t4b-login-form">
                        <div class="form-login">
                          <h3>JoomlArt Login</h3>
                            <form method="post" action="" id="admin-login-form">
                                <div class="alert alert-info">
                                    If you are free users, upgrade to <strong>Pro</strong> to access all <strong>Pro Website Bundles &amp; Blocks</strong>. <a href="https://www.joomlart.com/t4-page-builder" title="View Pricing" target="_blank">View Pricing</a></div>
                                <div class="t4b-login-form__username"><span class="add-on"><i class="fal fa-lock"></i></span><input type="text" id="t4b_login" name="am_login" size="18" value="" autofocus="" placeholder="Email or Username"></div>
                                <div class="t4b-login-form__password"><span class="add-on"><i class="fal fa-key"></i></span><input type="password" id="t4b_pass" name="am_passwd" size="18" placeholder="Password"></div>
                                <div id="t4b-login-form__message" class="alert alert-error" style="display:none;"></div>
                                <div class="t4b-login-form__actions"><input class="btn btn-primary" onclick="t4bLogin(event)" type="submit" name="submit" value="Log In"></div>
                                <div class="t4b-login-form__links"><a class="btn-link" href="https://www.joomlart.com/member/signup/free" target="_blank" title="Free Signup">Don't have account</a><a class="btn-link" href="https://www.joomlart.com/member/login?sendpass" target="_blank" title="Lost your password">Forgot password?</a></div>
                            </form>
                        </div>
                    </div>`;
        sidebar.append(loginform);
    }else{
        var t4b_username = t4bConfig.name.trim() ? t4bConfig.name.trim() : t4bConfig.username;
        var logout = `<div class="t4b-users-content">
            <div class="t4b-user-info">
                <span class="user-avatar">
                    <img src="https://static.themepro.com/images/default/avatars/avatar.jpg" class="t4b-user-avatar">
                </span>
                <span class="user">
                    <strong class="user-name"><a href="https://member.joomlart.com/member/" target="_blank" title="Visit Your Account Page" class="t4b-link-blank-target">${t4b_username}</a></strong>
                    <span class="user-badge badge-premium">${t4bConfig.user_type}</span>
                </span>
            </div>
            <div class="t4b-user-actions">
                <p>Your membership will expire on <strong>${t4bConfig.expire_date}</strong>.</p>
                <div class="btns">
                    <a class="btn btn-logout-ds" href="#" onclick="t4bLogout()" title="Disconnect page">Logout</a>
                        ${(t4bConfig.user_type_pk !== 'free') ? '<a class="btn btn-success btn-renew" href="https://www.joomlart.com/member" title="Renew Now!" target="_blank">Renew Now</a>' : '<a href="https://www.joomlart.com/t4-page-builder" target="_blank" class="btn btn-success btn-upgrade">Go to Pro</a>'} 
                    
                </div>
            </div>
        </div>`;
        sidebar.append(logout);
    }

     //button 
     var button_bottom = `
        <div class="t4b-info">
            <div class="button-action">
                <ul>   
                    <li><a class="btn btn-default" target="_blank" href="https://www.joomlart.com/forums/t/t4-builder" title="Support"><i class="fal fa-life-ring"></i>Support</a></li>  
                    <li><a class="btn btn-default" target="_blank" href="https://www.joomlart.com/documentation/t4-page-builder/getting-started" title="Docs"><i class="fal fa-file-alt"></i>Docs</a></li>  
                </ul>
            </div>
        </div>`;
     sidebar.append(button_bottom);
     $(document).find('.t4b-info').prepend($('.t4b-more-info').css({display:'block'}));


    //update action 
   /* $(document).on('click',"[data-dismis='modal']",function(){
        console.log('xx');
    });*/
    // init selected
    var uploading   = false;
    var dragZone    = $('#dragarea');
    var fileInput   = $('#install_package');
    var button      = $('#select-file-button');
    var pageExtra   = $('.t4b-pages');
    var url         = url_base + "/index.php?option=com_t4pagebuilder&view=page&format=json&act=import";
    var returnUrl   = $('#installer-return').val();
    var actions     = $('.upload-actions');
    var progress    = $('.upload-progress');
    var progressBar = progress.find('.bar');
    var percentage  = progress.find('.uploading-number');

    // Joomla.submitbuttonpackage();
    var showError = function(res) {
        dragZone.attr('data-state', 'pending');

        var message = Joomla.JText._('PLG_INSTALLER_PACKAGEINSTALLER_UPLOAD_ERROR_UNKNOWN');

        if (res == null) {
            message = Joomla.JText._('PLG_INSTALLER_PACKAGEINSTALLER_UPLOAD_ERROR_EMPTY');
        } else if (typeof res === 'string') {
            // Let's remove unnecessary HTML
            message = res.replace(/(<([^>]+)>|\s+)/g, ' ');
        } else if (res.message) {
            message = res.message;
        }

        Joomla.renderMessages({error: [message]});
    }
    Joomla.submitbuttonpackage = function()
    {

        var form = document.getElementById("page_import");
        // do field validation 
        if (form.install_package.value == "")
        {
            alert("' . JText::_('PLG_INSTALLER_PACKAGEINSTALLER_NO_PACKAGE', true) . '");
        }
    };
    if (typeof FormData === 'undefined') {
        $('#legacy-uploader').show();
        $('#uploader-wrapper').hide();
        return;
    }
    var importAjax = function(data,url){
        var i = 0;
        $.ajax({
            url: url,
            data: data,
            type: 'post',
            processData: false,
            cache: false,
            contentType: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();

                progressBar.css('width', 0);
                progressBar.attr('aria-valuenow', 0);
                percentage.text(0);
                pageExtra.hide();
                // Upload progress
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        if (i == 0) {
                            i = 1;
                            var percentComplete = 0;
                            var id = setInterval(frame, 10);

                            function frame() {
                                if (percentComplete >= 100) {
                                    clearInterval(id);
                                    i = 0;
                                } else {
                                    percentComplete++;
                                    progressBar.css('width', percentComplete + '%');
                                    progressBar.attr('aria-valuenow', percentComplete);
                                    percentage.text(percentComplete);
                                }
                            }
                        }
                    }
                }, false);

                return xhr;
            }
        })
        .done(function (res) {
            progress.hide();
            // Handle extension fatal error
            if (!res || (!res.page && !res.package)) {
                $('.uploadform').hide();
                showError(res.error);
                return;
            }

            pageExtra.show();
            jQuery('.t4b-libs-step2').toggle();
            let step2Html = "";
            res.page.forEach(data => {
                step2Html += "<li class=\"item\"><div class=\"item-inner\">";

                if(data.thumb){
                    step2Html += '<div class="thumb"><img src="'+data.thumb+'" alt="'+data.title+'"  title="'+data.title+'"  /></div>';
                }else{
                    step2Html += '<div class="thumb"><span class="place-holder"><i class="fal fa-file-alt"></i>'+ data.title +'</span></div>';
                }   
                step2Html += '<div class="name"><label class="custom-checkbox"><input type="checkbox" class="upload_cb2" id="cb2" name="jform[cid]" value="'+data.id+'" data-title="'+data.title+'" onclick="Joomla.isChecked(this.checked);"><span class="checkmark"><i class="fal fa-check"></i></span></label>'+data.title+'</div>';
                step2Html += "</div></li>";
            });
            $('#uploader-wrapper').hide();
            $('#step-2').find('.page-list').empty();
            $('#step-2').find('.page-list').append($(step2Html));
            $('#step-2').find('#package-loaded').val(JSON.stringify(res.package));
            $('#step-2').show();

        }).fail(function (error) {
            uploading = false;
            if (error.status === 200) {
                var res = error.responseText || error.responseJSON;
                showError(res);
            } else {
                showError(error.statusText);
            }
        });
    }
    $(document).on("click",'.btn-page-import',function(e){
        let data = {};
        let fav =[];
        let package = $('#package-loaded').val();
        if(!package){
            Joomla.renderMessages({error:["No package!!!!!!"]});
            return false;
        }
        $.each($("input[name='jform[cid]']:checked"), function(){            
            fav.push($(this).val());
        });
        if(!fav.length){
            Joomla.renderMessages({error:[" No page selected!!!!!!"]});
            return false;
        }
        data.page_import = fav;
        data.package =  package;
        urlStep2 = url + "&st=step2";
        $.ajax({
            url: urlStep2,
            data: JSON.stringify(data),
            type: 'post',
            processData: false,
            cache: false,
            contentType: "application/json",
            xhr: function () {
                var xhr = new window.XMLHttpRequest();

                progressBar.css('width', 0);
                progressBar.attr('aria-valuenow', 0);
                percentage.text(0);

                // Upload progress
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        var number = Math.round(percentComplete * 100);
                        progressBar.css('width', number + '%');
                        progressBar.attr('aria-valuenow', number);
                        percentage.text(number);

                        if (number === 100) {
                            dragZone.attr('data-state', 'installing');
                        }
                    }
                }, false);

                return xhr;
            }
        })
        .done(function (res) {
            $('.t4b-action-wrap').find('button').hide();
            $('.t4b-action-wrap').append("<button type='button' class='btn btn-primary btn-import-close'>Ok(3)</button>");
            var counter = 5;
            var interval = setInterval(function() {
              counter--;
              $(".btn-import-close").html("Ok("+counter+")");
              if (counter === 0) {
                $('#importpage').modal('hide');
                clearInterval(interval);
                window.parent.location.reload();
              }
            }, 1000);
            // $('#importpage').modal('hide');
            
            console.log(res.message)
            Joomla.renderMessages({'success': [res.message]});

        }).error(function (error) {
            Joomla.renderMessages({'error': [error]});
        });
    });
    $(document).on('click','.btn-import-close',function(e){
        $('#importpage').modal('hide');
        window.parent.location.reload();
    });
    if (returnUrl) {
        url += '&return=' + returnUrl;
    }

    button.on('click', function(e) {
        fileInput.click();
    });

    fileInput.on('change', function (e) {
        if (uploading) {
            return;
        }
        var files = e.originalEvent.target.files || e.originalEvent.dataTransfer.files;
        if (!files.length) {
            return;
        }
        var file = files[0];
        if(!validate_fileupload(file)){
            return;
        }
        var data = new FormData;
        data.append('install_package', file);
        data.append('installtype', 'upload');
        let urlStep1 = url + "&st=step1";
        importAjax(data,urlStep1);
        $('#uploader-wrapper').toggle();
        
    });

    dragZone.on('dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();

        dragZone.addClass('hover');

        return false;
    });

    // Notify user when file is over the drop area
    dragZone.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();

        dragZone.addClass('hover');

        return false;
    });

    dragZone.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dragZone.removeClass('hover');

        return false;
    });

    dragZone.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();

        dragZone.removeClass('hover');

        if (uploading) {
            return;
        }

        var files = e.originalEvent.target.files || e.originalEvent.dataTransfer.files;

        if (!files.length) {
            return;
        }

        var file = files[0];
        if(!validate_fileupload(file)){
            return;
        }
        var data = new FormData;
        data.append('install_package', file);
        data.append('installtype', 'upload');
        data.append('filename',file['name']);
        dragZone.attr('data-state', 'uploading');
        uploading = true;
         let urlStep1 = url + "&st=step1";
        importAjax(data,urlStep1);

        function showError(res) {
            dragZone.attr('data-state', 'pending');

            var message = Joomla.JText._('PLG_INSTALLER_PACKAGEINSTALLER_UPLOAD_ERROR_UNKNOWN');

            if (res == null) {
                message = Joomla.JText._('PLG_INSTALLER_PACKAGEINSTALLER_UPLOAD_ERROR_EMPTY');
            } else if (typeof res === 'string') {
                // Let's remove unnecessary HTML
                message = res.replace(/(<([^>]+)>|\s+)/g, ' ');
            } else if (res.message) {
                message = res.message;
            }

            Joomla.renderMessages({error: [message]});
        }
    });
    //check impport openning
    if(localStorage.getItem('importOpened') == true){
        $('#toolbar-Import button').click();
        localStorage.setItem('importOpened', false);
    }
    $(document).on('click',"#step-2 .thumb", function(e){
        $(this).closest('.item').find('.upload_cb2').click();
    });
    $(document).on('click','.upload_checkall',function(e){  
        if(!$(this).prop('checked')){
            $('.upload_cb2').prop('checked',false);
        }else{
            $('.upload_cb2').prop('checked',true);
        }
        
    });
    $(document).on('change',".upload_checkall,.upload_cb2",function(){

        var allCb2 = $('.upload_cb2').map(function(){
            if($(this).prop('checked')){
                return this.value;
            }else{
                $('#checkall').prop('checked',false);
            }
        }).get();
        if(allCb2.length == $('.upload_cb2').length){
            $('.upload_checkall').prop('checked',true);
        }
        if(allCb2.length){
            $('.t4b-action-wrap').show();
        }else{
            $('.t4b-action-wrap').hide();
        }
    });
    
    //check sidebar
    processScrollBar();
    $(window).on('scroll', processScrollBar);
});
function processScrollBar() {
    if (jQuery('.subhead').length) {
        if(jQuery('.subhead').hasClass('subhead-fixed')){
            jQuery('#jpb-sidebar').addClass('t4b-sidebar-fixed');
        }else{
            jQuery('#jpb-sidebar').removeClass('t4b-sidebar-fixed');
        }
    }
}
function validate_fileupload(file)
{
    var fileName = file.name;
    var fileSize = (file.size/(1024*1024)).toFixed(2);
    var allowed_extensions = new Array("json","zip");
    
    var file_extension = fileName.split('.').pop().toLowerCase(); 
    if(allowed_extensions.indexOf(file_extension) == -1)
    {
        Joomla.renderMessages({error: ['File type not supported!']});
        return false;
    }
    return true;
}
var t4bLogout = function(){
     var remote_url = "https://www.joomlart.com/";
    localStorage.setItem('t4bConfig',JSON.stringify({'login_status':false,'remote_url':remote_url}));
    window.location.reload();
}        
var t4bLogin = function(e){
    e.preventDefault();
    var login = encodeURIComponent(jQuery("#t4b_login").val()), pass = encodeURIComponent(jQuery('#t4b_pass').val());
    var t4bConfig = JSON.parse(localStorage.getItem('t4bConfig')) || {};
    postData(`${t4bConfig.remote_url}member/builderbundle/get-access-token?login=${login}&pass=${pass}`).then(data => {
        if (data.status == '200') {
            data.data.login_status = true;
            data.data.remote_url = t4bConfig.remote_url;
            if(data.data.user_type.toLowerCase() != 'free'){
                data.data.user_type_pk = "pro";
            }else{
                data.data.user_type_pk = "free";
            }
            localStorage.setItem('t4bConfig', JSON.stringify(data.data));
            window.location.reload();
        } else {
            jQuery('#t4b-login-form').find('#t4b-login-form__message').html(data.message);
            jQuery('#t4b-login-form').find('#t4b-login-form__message').css({display:"block"});
            return false;
        }

    });
}
// Example POST method implementation:
async function postData(url = '',type = "GET", data = {}) {
    if(type == 'POST'){
        // Default options are marked with *
        const response = await fetch(url, {
            method: "POST", // *GET, POST, PUT, DELETE, etc.
            mode: 'cors', // no-cors, *cors, same-origin
            cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
            credentials: 'same-origin', // include, *same-origin, omit
            headers: {
              'Content-Type': 'application/json',
            },
            redirect: 'follow', // manual, *follow, error
            referrerPolicy: 'no-referrer', // no-referrer, *client
            body: JSON.stringify(data) // body data type must match "Content-Type" header
        });
        return await response.json(); // parses JSON response into native JavaScript objects
    }
    if(type == 'GET'){
        const get = await fetch(url);
        return await get.json();
    }
}
var t4b = window.t4b || {};
;(function($){
    'use strict';
    t4b.updateBtn = function($el,config){
        if(!config.login_status){
            $('#toolbar-updateStyle').remove();
            return;
        }
        $('#toolbar-updateStyle').show();
        $(document).on('click','#toolbar-updateStyle',function(){
            if (document.adminForm.boxchecked.value == 0) { 
                alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')); 
            } else { 
                if(config.user_type_pk == 'free'){
                   Joomla.renderMessages({message:['Your account does not have permission to update.']});
                   return;
                }
                $('#jpb-main-container').addClass('page-loading');
                $.each($("input[name='cid[]']:checked"), function(){
                    t4b.config.pageId.push($(this).val());
                    t4b.getPageKey($(this).val());
                });
            }
        });
    };
    t4b.config = {
        pageId : [],
        pageUpdate : [],
        pageMesg : {
            "success":[],
            "error":[]
        }
    };

    t4b.pageDetail = function(){
        var pageData = localStorage.getItem('t4bpage');
        var allPageDetail = [];
        if(!pageData) return;
        var data = JSON.parse(pageData);
        data.forEach(page => {
            var items = JSON.parse(page.detail);
            items.forEach(e => {e.package_id = page.id });
            allPageDetail = [...items, ...allPageDetail];
        });

        return allPageDetail;
    };
    t4b.getPageKey = function(id){
        var url = url_base + "/index.php?option=com_t4pagebuilder&view=page&format=json&act=Pagekey&func=getkey&id="+id;
        postData(url).then(data => {
            var page_key = data.data.id;
            var page_title = data.data.title;
            t4b.updatePage(page_key,id,page_title);
            

        });
    }
    t4b.renderMesg = $('#system-message-container');
    t4b.updatePage = function($key, id, page_title){
        if(!t4b.renderMesg.length) t4b.renderMesg = $('#system-message-container');
        if(!$key){

            t4b.config.pageUpdate.push(id);
            t4b.config.pageMesg.error.push("Page key of \""+ page_title + "\" page is empty or invalid.");
            t4b.renderMessages();
            return;   
        }
        var allPageData = t4b.pageDetail();
        var item_id = [];
        var Item = allPageData.find(item => item.id == $key);
        if(!Item) {
            t4b.config.pageUpdate.push(id);
            t4b.config.pageMesg.error.push("Page key of \""+ page_title + "\" page is empty or invalid.");
            t4b.renderMessages();
            return;
        }
        item_id.push(Item.id);
        var url = url_base + "/index.php?option=com_t4pagebuilder&view=page&format=json&act=import&type=package";
        var config = JSON.parse(localStorage.getItem('t4bConfig'));
        var package_url = config.remote_url+"member/builderbundle/download/?user_id=" + config.user_id + "&item_id=" +Item.package_id + "&token=" + config.user_token;
        var data_page = new FormData();
        data_page.append('package_url', package_url);
        data_page.append('installtype', 'url');
        data_page.append('page_import', JSON.stringify(item_id));
        data_page.append('page_style',1);
        data_page.append('page_site_id', id);

        this.importPage(data_page,url, page_title);

    }
    t4b.importPage = function(data, url, page_title){
        var i = 0;
        var that = this;
        var progress = jQuery('.package-progress');
        var pageExtra = jQuery('.t4b-pages');
        var backtolibs = jQuery('.btn-link.btn-back');
        var progressBar = progress.find('.bar');
        var percentage = progress.find('.uploading-number');
        var percentComplete = 0;
        var completed = false;
        jQuery.ajax({
            url: url,
            data: data,
            type: 'post',
            processData: false,
            cache: false,
            contentType: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {

                }, false);
                return xhr;
            }
        })
        .done(function(res) {
            // progress.hide();
            // Handle extension fatal error
            if (res.error) {
                t4b.config.pageUpdate.push('error');
                t4b.config.pageMesg.error.push(res.error+" Joomlart login session expired!");
                t4b.renderMessages();
            }
            if(res.data){
                t4b.config.pageUpdate.push(res.data.id);
                t4b.config.pageMesg.success.push("\""+ res.data.title + "\" page style has been updated successfully!");
                t4b.renderMessages();

            }
        });
    };
    t4b.renderMessages = function(){
        if(t4b.config.pageId.length == t4b.config.pageUpdate.length){
            if(t4b.config.pageMesg.success.length && t4b.config.pageMesg.error.length){
                Joomla.renderMessages({success: t4b.config.pageMesg.success,error: t4b.config.pageMesg.error});
            }else{
                if(t4b.config.pageMesg.success.length){
                    Joomla.renderMessages({success: t4b.config.pageMesg.success});
                }
                if(t4b.config.pageMesg.error.length){
                    Joomla.renderMessages({error: t4b.config.pageMesg.error});
                }
            }
            //remove checked
            var check_input  = $('#page-list tbody').find('input');
            check_input.each(index => {
                if($(check_input[index]).prop('checked')){
                    $(check_input[index]).prop('checked',false)
                }
            });
            t4b.config = {
                pageId : [],
                pageUpdate : [],
                pageMesg : {
                    "success":[],
                    "error":[]
                }
            };
            $("#jpb-main-container").removeClass('page-loading');

        }
    }
})(jQuery)