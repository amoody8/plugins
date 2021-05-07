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

namespace JPB\Editor\Action;
defined('_JEXEC') or die;
use Joomla\CMS\Factory as JFactory;

class Base {

	public function __construct () {
	}

	public function run () {
		return null;
	}

	protected function getRemoteFile($url)
	{
		$config = JFactory::getConfig();
		// Capture PHP errors
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);

		// Set user agent
		$version = new \JVersion;
		ini_set('user_agent', $version->getUserAgent('Installer'));

		$headers = array();

		try
		{
			$response = \JHttpFactory::getHttp()->get($url, $headers);
		}
		catch (RuntimeException $exception)
		{
			$error = $exception->getMessage();
			if (preg_match('/ssl/i', $error)) {
				// ssl error, try with non-ssl
				$url = preg_replace ('/^https/', 'http', $url);
				return $this->getRemoteFile($url);
			}
			return false;
		}

		if (302 == $response->code && isset($response->headers['Location']))
		{
			return $this->getRemoteFile($response->headers['Location']);
		}
		elseif (200 != $response->code)
		{
			return false;
		}

		return $response->body;
	}	

}