<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/
defined('_JEXEC') or die();
?>

<div class="cModule cProfile-Friends app-box">
	<h3 class="app-box-header"><?php echo JText::_('COM_COMMUNITY_PROFILE_VIDEO_TITLE'); ?></h3>
	<div class="app-box-content">
		<div class="video-player">
			<?php echo $video->getPlayerHTML('auto',200); ?>
		</div>
	</div>
</div>
