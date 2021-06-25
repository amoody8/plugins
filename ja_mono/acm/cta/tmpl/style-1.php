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

  $ctaStyle       = $helper->get('cta-style');
  $ctaHeading     = $helper->get('cta-heading');
  $ctaIntro       = $helper->get('cta-intro');
  $btnText        = $helper->get('cta-btn-text');
  $btnLink        = $helper->get('cta-btn-link');
  $ctaBg          = $helper->get('cta-bg');
?>

<div class="acm-cta <?php echo $ctaStyle; ?> <?php if( trim($ctaHeading) ) echo ' show-intro'; ?>" style="background-image: url(<?php echo trim($ctaBg); ?>);">
  <div class="cta-content">
  
    <?php if( trim($ctaHeading)) : ?>
    <div class="cta-heading">
      <?php echo $ctaHeading; ?>
    </div>
    <?php endif; ?>
    
    <?php if( trim($ctaIntro)) : ?>
    <div class="cta-intro">
      <?php echo $ctaIntro; ?>
    </div>
    <?php endif; ?>
    
    <?php if( trim($btnText)): ?>
    <div class="cta-btn-actions">
      <a href="<?php echo trim($btnLink); ?>" title="<?php echo trim($btnText); ?>" class="btn-flip-wrap">
        <span class="btn btn-border-light btn-flip-front"><?php echo trim($btnText); ?></span>
        <span class="btn btn-flip-back"><?php echo JText::_('TPL_LET_GO'); ?></span>
      </a>
    </div>
    <?php endif; ?>
  </div>
</div>