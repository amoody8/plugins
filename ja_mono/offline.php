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
$app = JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0;">
	<link href='https://fonts.googleapis.com/css?family=Karla:400,700' rel='stylesheet' type='text/css'>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/offline.css" type="text/css" />
	<?php if ($this->direction == 'rtl') : ?>
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/offline_rtl.css" type="text/css" />
	<?php endif; ?>

</head>
<body>
	
	<div id="frame" class="outline">
		<div class="row">
			<div class="col-sm-12 col-md-6 block-left">
				<div class="info-offline">
					<div id="offline-title">
						<h1><?php echo htmlspecialchars($app->getCfg('sitename')); ?></h1>
					</div>
					
					<?php if ($app->getCfg('display_offline_message', 1) == 1 && str_replace(' ', '', $app->getCfg('offline_message')) != ''): ?>
						<div class="des-offline">
							<div class="offline-des">
								<?php echo $app->getCfg('offline_message'); ?>
							</div>
						</div>

					<?php elseif ($app->getCfg('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != ''): ?>
						<div>
							<?php echo JText::_('JOFFLINE_MESSAGE'); ?>
						</div>
					<?php  endif; ?>
				</div>
			</div>
			
			<div class="col-sm-12 col-md-6 block-right">
				<div id="offline-content">
					<jdoc:include type="message" />
					
					<?php if ($app->getCfg('offline_image') && file_exists($app->getCfg('offline_image'))) : ?>
						<div id="offline-img"><img src="<?php echo $app->getCfg('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->getCfg('sitename')); ?>" /></div>
					<?php endif; ?>
				
					<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
					<div class="input">
							<div class="form-offline">
								<div id="form-login-username" class="form-group" >
									<label class="control-label" for="username"><i class="fa fa-user"></i></label>
									<input class="control-input" name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" size="18" />
								</div>
								<div id="form-login-password" class="form-group" >
									<label class="control-label" for="passwd"><i class="fa fa-key"></i></label>
									<input class="control-input" type="password" name="password" class="inputbox" size="18" alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" id="passwd"/>
								</div>
							</div>
						<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
						<div id="form-login-remember">
							<input type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" id="remember" />
							<label for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
						</div>
						<?php  endif; ?>
						<div id="submit-buton">
							<input type="submit" name="Submit" class="button login" value="<?php echo JText::_('JLOGIN') ?>" />
						</div>
						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.login" />
						<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	
</body>
</html>
