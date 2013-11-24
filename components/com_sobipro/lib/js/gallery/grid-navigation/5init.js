/**
 * @author
 * Name: Mostafa Shalkami
 * Email: info[at]sobimarket.net
 * @copyright Copyright (C) 2012 Mostafa Shalkami. All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl.html GNU/GPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 3
 */
jQuery(function() {
				jQuery('#tj_container').gridnav({
					rows	: 2,
					type	: {
						mode		: 'sequpdown', 		// use def | fade | seqfade | updown | sequpdown | showhide | disperse | rows
						speed		: 500,				// for fade, seqfade, updown, sequpdown, showhide, disperse, rows
						easing		: '',				// for fade, seqfade, updown, sequpdown, showhide, disperse, rows	
						factor		: 50,				// for seqfade, sequpdown, rows
						reverse		: false				// for sequpdown
					}
				});
				jQuery("a.colorbox").colorbox({rel:"colorbox"});
			});
