<?php
/**
 * ------------------------------------------------------------------------
 * JA Mono Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
 
	$style							= $helper->get('acm-style');
	$count 							= $helper->getRows('client-item.client-logo');
	$gray								= $helper->get('img-gray');
	$opacity						= $helper->get('img-opacity');
	$float = 0;
	
	if ($opacity=="") {
		$opacity = 100;
	}

	$columns = 5;
	 
?>

<div id="uber-cliens-<?php echo $module->id; ?>" class="uber-cliens style-1 <?php if($gray): ?> img-grayscale <?php endif; ?> <?php echo $style; ?>">
		<div class="row owl-carousel owl-slider">
	 <?php 
	 	for ($i=0; $i<$count; $i++) : 
	 	
		$clientName = $helper->get('client-item.client-name',$i);
		$clientLink = $helper->get('client-item.client-link',$i);
		$clientLogo = $helper->get('client-item.client-logo',$i);
		
	?>
	
		<div class="client-item" >
			<div class="client-img">
				<?php if($clientLink):?><a href="<?php echo $clientLink; ?>" title="<?php echo $clientName; ?>" ><?php endif; ?>
					<img class="img-responsive" alt="<?php echo $clientName; ?>" src="<?php echo $clientLogo; ?>">
				<?php if($clientLink):?></a><?php endif; ?>
			</div>
		</div> 
	 	
 	<?php endfor ?>
 	</div>
</div>

<?php if($opacity>=0 && $opacity<=100): ?>
<script>
(function ($) {
	$(document).ready(function(){ 
		$('#uber-cliens-<?php echo $module->id ?> .client-img img.img-responsive').css({
			'filter':'alpha(opacity=<?php echo $opacity ?>)', 
			'zoom':'1', 
			'opacity':'<?php echo $opacity/100 ?>'
		});

		$("html[dir='ltr'] #uber-cliens-<?php echo $module->id; ?> .owl-carousel").owlCarousel({
      nav : false,
      dots: false,
      items: <?php echo $columns; ?>,
      loop: false,
      responsive : {
        // breakpoint from 0 up
        0 : {
            items : 2
        },
        
        // breakpoint from 480 up
        480 : {
            items : 3
        },
        
        // breakpoint from 768 up
        769 : {
            items : 4
        },

        // breakpoint from 1199 up
        1200 : {
            items : 5
        }
      }
    });

    $("html[dir='rtl'] #uber-cliens-<?php echo $module->id; ?> .owl-carousel").owlCarousel({
      nav : false,
      dots: false,
      items: <?php echo $columns; ?>,
      loop: false,
      rtl: true,
      responsive : {
        // breakpoint from 0 up
        0 : {
            items : 2
        },
        
        // breakpoint from 480 up
        480 : {
            items : 3
        },
        
        // breakpoint from 768 up
        769 : {
            items : 4
        },

        // breakpoint from 1199 up
        1200 : {
            items : 5
        }
      }
    });
	});
})(jQuery);
</script>
<?php endif; ?>
<?php 
  $inner_modules = 'cta';
  $attrs = array();
  $attrs['style'] = 'raw';
  $result = null;
  $renderer = JFactory::getDocument()->loadRenderer('modules');
  $inner = $renderer->render($inner_modules, $attrs, $result); 
  echo $inner;
?>