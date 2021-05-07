/** 
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github 
 *                & Google group to become co-author)
 *------------------------------------------------------------------------------
 */

(function ($) {
	$(document).ready(function () {
		var $previewItem = $('.t4-preview-item'),
			currentUrl = location.href.split('#')[0],
			previewParam = 't4doc=preview',
			checkUrl = currentUrl.replace(previewParam, 't4doc=previewUpdate') + (currentUrl.match(/\?/) ? '&' : '?') + 't4preview-check=',
			params = new URLSearchParams(window.location.search),
			previewId = params.get('t4id')
			;

		// Setup live reload
		if ($previewItem.length) {
			// wrapp in t4 class
			$previewItem.addClass('t4');

			var checkUpdate = function () {
				$previewItem.each(function() {
					var $item = $(this);
					$.getJSON(checkUrl + $item.data('update'), function (data) {
						if (!data.update) return;
						$item.data('update', data.update).html(getContent(data, $item.data('type'))).addClass('updating');
						setTimeout(() => {$item.removeClass('updating')}, 1000);
						updateLinks();
					})
				})
			}

			window.checkUpdate = checkUpdate;

			window.checkUpdateInterval = setInterval (checkUpdate, 5000);
			checkUpdate();
		} else {
			alert ('Preview Item not found!')
		}

		var getContent = function (data, type) {
			console.log(data);
			var content = (type == 'intro' || type == 'full') ? data[type] : data.intro + data.full;
			var css = (data.css ? data.css : "") + (data.blockscss ? data.blockscss : "");
			return content + (css ? '<style>' + css + '</style>' : '');
		}

		// update all link with preview id
		var updateLinks = function() {

			$('a').each((i, link) => {
				var urls = link.href.split('#'),
					toUrl = urls[0],
					baseUrl = location.protocol + '//' + location.host + (location.port ? ':' + location.port : '');
				if (toUrl.indexOf(baseUrl) == 0) {	
					if (toUrl.indexOf(previewParam) == -1) {
						toUrl += (toUrl.match(/\?/) ? '&' : '?') + previewParam + '&t4id=' + previewId;
					}
					if (urls.length > 1) toUrl += '#' + urls[1];
					link.href = toUrl;
				}
			})

		}

		updateLinks();

	})


}) (jQuery)