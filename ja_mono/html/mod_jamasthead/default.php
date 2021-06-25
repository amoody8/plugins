<?php
/**
 * ------------------------------------------------------------------------
 * JA Mono Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
defined('_JEXEC') or die('Restricted access');
$mh_background = new stdClass();
$mh_background->url = '';
$mh_background->type = '';
//var_dump($masthead);
//Check the background type is image background or video background
if(isset($masthead['params']['background']) && !empty($masthead['params']['background'])){
    $mh_background->url = rtrim($masthead['params']['background']);
    if(preg_match('/^.*\.(mp4|ogg|ogv|webm)$/', $mh_background->url)){
        $mh_background->type = 'video';
    }else{
        $mh_background->type = 'image';
    }
}
?>

<div id="ja-masthead" class="jamasthead<?php echo $params->get('moduleclass_sfx','')?> <?php if($params->get ('enable-blend') == "1") : ?>blend-bg<?php endif ;?>" <?php if ($mh_background && $mh_background->type == 'image') : ?> style="background-image: url('<?php echo $mh_background->url; ?>')"<?php endif; ?>>
	<div id="particles-js" class="<?php if($params->get ('enable-effect') != "1") : ?>hide<?php endif ?>"></div>
	<?php
        //Video background 
        if($mh_background && $mh_background->type == 'video') : 
        preg_match_all('/^.*\.(mp4|ogg|ogv|webm)$/', $mh_background->url, $matches);
        if($matches[1][0] && $matches[1][0] === 'ogv'){
            $matches[1][0] = 'ogg';
        }
    ?>
        <div id="ja-masthead-bg" style="position: absolute; overflow: hidden; top:0; width: 100%; height: 100%; z-index:  -1;">
            <video id="ja_masthead_bg_video" loop="true" autoplay="true" style="width:100%">
                <source type="video/<?php echo $matches[1][0] ?>" src="<?php echo $mh_background->url ?>" />
            </video>
        </div>
        <script type="text/javascript">
            jQuery(window).load(function($){
                jQuery("div.jamasthead"<?php echo $params->get('moduleclass_sfx','')?>).css("background-image","none");
                jQuery(".ja-masthead<?php echo $params->get('moduleclass_sfx','')?>").addClass("masthead-video");
                if(jQuery("#ja-masthead").hasClass('blend-bg')){
                    jQuery("#ja-masthead").removeClass('blend-bg');
                }
                
                //Responsive for background-image
                var vid = jQuery("video#ja_masthead_bg_video");
                vid.onloadeddata = function() {
                    wresize();
                };
                
                    
                jQuery(window).resize(function(){
                    wresize();
                });
                
                function wresize(){
                    var video = jQuery("video#ja_masthead_bg_video");
                    var videoHeight = video.get(0).videoHeight;
                    var videoWidth = video.get(0).videoWidth;
                    var videoAspect = videoHeight / videoWidth;
                    var dHeight = jQuery("#ja-masthead-bg").height();
                    var dWidth = jQuery("#ja-masthead-bg").width();
                    var divAspect = dHeight / dWidth;
                    
                    if(videoAspect > divAspect){
                        videoWidth = dWidth;
                        videoHeight = videoWidth * videoAspect;
                    
                        video.css('width',videoWidth+'px');
                        video.css('height',videoHeight+'px');
                        video.css('top','-'+(videoHeight-dHeight)/2+'px');
                        video.css('margin-left','0');
                    }else{
                        videoHeight = dHeight;
                        videoWidth = videoHeight / videoAspect;
                        video.css('width',videoWidth+'px');
                        video.css('height',videoHeight+'px');
                        video.css('margin-left','-'+(videoWidth-dWidth)/2+'px');
                        video.css('margin-top','0');
                    }
                }
            });
        </script>
    <?php endif; ?>
    
    <div class="jamasthead-detail">
		<h3 class="jamasthead-title"><?php echo $masthead['title']; ?></h3>
        <?php if ($masthead['description'] != '') : ?>
		<div class="jamasthead-description"><?php echo $masthead['description']; ?></div>
        <?php endif; ?>
	</div>
</div>

<?php if($params->get ('enable-effect') == "1") : ?>
<script type="text/javascript" src="<?php echo T3_TEMPLATE_URL ?>/js/particles.js"></script>
<script type="text/javascript" src="<?php echo T3_TEMPLATE_URL ?>/js/app.js"></script>
<script type="text/javascript" src="<?php echo T3_TEMPLATE_URL ?>/js/stats.js"></script>
<?php endif ;?>