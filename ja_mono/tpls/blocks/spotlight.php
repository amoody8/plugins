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

// No direct access
defined('_JEXEC') or die;
?>
<?php
	$name      = $vars['name'];
	$splparams = $vars['splparams'];
	$datas     = $vars['datas'];
	$cols      = $vars['cols'];
	$addcls    = isset($vars['class']) ? $vars['class'] : '';
	$style     = isset($vars['style']) && $vars['style'] ? $vars['style'] : 'T3Xhtml';
	$tstyles   = explode(',', $style);

	if(count($tstyles) == 1){
		$styles = array_fill(0, $cols, $style);
	} else {

		$styles = array_fill(0, $cols, 'T3Xhtml');
		foreach ($tstyles as $i => $stl) {
			if(trim($stl)){
				$styles[$i] = trim($stl);
			}
		}
	}
	?>
	<!-- SPOTLIGHT -->
	<div class="t3-spotlight t3-<?php echo $name, ' ', $addcls, ' ', T3_BASE_ROW_FLUID_PREFIX ?> equal-height">
		<?php
		foreach ($splparams as $i => $splparam):
			$param = (object)$splparam;
		?>
			<div class="col <?php echo $datas[$i] ?>">
				<?php if ($this->countModules($param->position)) : ?>
				<jdoc:include type="modules" name="<?php echo $param->position ?>" style="<?php echo $styles[$i] ?>"/>
				<?php else: ?>
				&nbsp;
				<?php endif ?>
			</div>
		<?php endforeach ?>
	</div>
<!-- SPOTLIGHT -->