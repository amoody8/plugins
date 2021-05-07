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
?>
<div class="jpb-page-wrap">
	<?php
		$xml = JPATH_COMPONENT_ADMINISTRATOR . "/models/forms/page.xml";
		$this->form = JForm::getInstance('page',$xml);
		echo  JLayoutHelper::render('joomla.edit.title_alias', $this);
	?>
</div>