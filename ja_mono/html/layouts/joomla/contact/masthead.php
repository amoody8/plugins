<?php
/*
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
defined('_JEXEC') or die;
$params = $displayData['params'];
$item = $displayData['item'];
?>
<div class="jamasthead blend-bg contact" >
    <div class="jamasthead-detail plain-contact"><div class="plain-style">
      <!-- Contact images -->
      <div class="col-xs-12 contact-top">
        <?php if ($item->contact->image && $params->get('show_image')) : ?>
          <div class="contact-image">
            <?php echo JHtml::_('image', $item->contact->image, JText::_('COM_CONTACT_IMAGE_DETAILS'), array('align' => 'middle')); ?>
          </div>
        <?php endif; ?>
      <!-- End Contact images -->
      </div>

      <!-- Show Other information -->
      <div class="col-xs-12 contact-information">
        <div class="inner"> 
          <!-- Show Contact name -->
          <?php if ($item->contact->name && $item->params->get('show_name')) : ?>
            <div class="page-header">
              <h2>
                <?php echo $item->contact->name; ?>
              </h2>
            </div>
          <?php endif;  ?>
          <!-- End Show Contact name -->

          <!-- Contact other information -->
          <div class="box-contact contact-miscinfo">
            <h3><?php echo JText::_('COM_CONTACT_OTHER_INFORMATION');?></h3>
            <dl class="dl-horizontal">
              <dt>
                <span class="<?php echo $params->get('marker_class'); ?>">
                  <?php echo $params->get('marker_misc'); ?>
                </span>
              </dt>
              <dd>
                <span class="contact-misc">
                  <?php echo $item->contact->misc; ?>
                </span>
              </dd>
            </dl>
          </div>
          <!-- End other information -->
          
          <div class="box-contact"> 
            <!-- Contact links -->
            <?php echo $item->loadTemplate('links'); ?>
            <!-- End contact Links -->
          </div>
          
          <div class="box-contact"> 
            <!-- Contact -->
            <?php  echo '<h3>'. JText::_('COM_CONTACT_DETAILS').'</h3>';  ?>
            
            <?php if ($item->contact->con_position && $params->get('show_position')) : ?>
              <dl class="contact-position dl-horizontal">
                <dd>
                  <?php echo $item->contact->con_position; ?>
                </dd>
              </dl>
            <?php endif; ?>
            
            <?php echo $item->loadTemplate('address'); ?>
        
            <?php if ($params->get('allow_vcard')) :  ?>
              <?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS');?>
                <a href="<?php echo JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id='.$item->contact->id . '&amp;format=vcf'); ?>">
                <?php echo JText::_('COM_CONTACT_VCARD');?></a>
            <?php endif; ?>
            <!-- End contact-->
          </div>
        </div>
      </div>
      <!-- End Show --> 
    </div></div>
</div>