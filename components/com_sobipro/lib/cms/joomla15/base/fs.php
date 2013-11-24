<?php
/**
 * @version: $Id: fs.php 666 2011-01-28 19:16:48Z Radek Suski $
 * @package: SobiPro Bridge
 * ===================================================
 * @author
 * Name: Sigrid Suski & Radek Suski, Sigsiu.NET GmbH
 * Email: sobi[at]sigsiu.net
 * Url: http://www.Sigsiu.NET
 * ===================================================
 * @copyright Copyright (C) 2006 - 2011 Sigsiu.NET GmbH (http://www.sigsiu.net). All rights reserved.
 * @license see http://www.gnu.org/licenses/gpl.html GNU/GPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU General Public License version 3
 * ===================================================
 * $Date: 2011-01-28 20:16:48 +0100 (Fri, 28 Jan 2011) $
 * $Revision: 666 $
 * $Author: Radek Suski $
 * File location: components/com_sobipro/lib/cms/joomla15/base/fs.php $
 */
defined( 'SOBIPRO' ) || exit( 'Restricted access' );
require_once dirname(__FILE__).'/../../joomla_common/base/fs.php';

/**
 * Interface to Joomla! files system
 * @author Radek Suski
 * @version 1.0
 * @created 10-Jan-2009 5:02:55 PM
 */
abstract class SPFs extends SPJoomlaFs {}
?>