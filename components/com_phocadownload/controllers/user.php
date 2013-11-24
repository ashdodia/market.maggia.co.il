<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

class PhocaDownloadControllerUser extends PhocaDownloadController
{

	public $loginUrl;
	public $loginString;
	public $url;
	public $itemId;
	
	
	function __construct() {
		parent::__construct();
		$this->registerTask( 'unpublish', 'unpublish' );
		
		$this->itemId			= JRequest::getVar( 'Itemid', 0, '', 'int' );
		$this->loginUrl			= JRoute::_('index.php?option=com_users&view=login', false);
		$this->loginString		= JText::_('COM_PHOCADOWNLOAD_NOT_AUTHORISED_ACTION');
		$this->url				= 'index.php?option=com_phocadownload&view=user&Itemid='. $this->itemId;
	}
	/*
	function display() {
		if ( ! JRequest::getCmd( 'view' ) ) {
			JRequest::setVar('view', 'user' );
		}
		parent::display();
    }*/
	
	function unpublish() {
		
		$post['id']			= JRequest::getVar( 'actionid', '', 'post', 'int', 0  );
		$post['limitstart']	= JRequest::getVar( 'limitstart', '', 'post', 'int', 0  );
		$model 				= $this->getModel('user');
		//$isOwnerCategory 	= 1;//$model->isOwnerCategoryImage((int)$this->_user->id, (int)$id);
		// USER RIGHT - Delete - - - - - - - - - - -
		// 2, 2 means that user access will be ignored in function getUserRight for display Delete button
		$user = JFactory::getUser();
		$rightDisplayDelete	= 0;
		$catAccess	= PhocaDownloadHelper::getCategoryAccessByFileId((int)$post['id']);
		if (!empty($catAccess)) {
			$rightDisplayDelete = PhocaDownloadHelper::getUserRight('deleteuserid', $catAccess->deleteuserid, 2, $user->authorisedLevels(), $user->get('id', 0), 0);
		}
		// - - - - - - - - - - - - - - - - - - - - - -
		
		if ($rightDisplayDelete) {
			if(!$model->publish((int)$post['id'], 0)) {
			$msg = JText::_('COM_PHOCADOWNLOAD_ERROR_UNPUBLISHING_ITEM');
			} else {
			$msg = JText::_('COM_PHOCADOWNLOAD_SUCCESS_UNPUBLISHING_ITEM');
			} 
		} else {
			$app	= JFactory::getApplication();
			$app->redirect($this->loginUrl, $this->loginString);
			exit;
		}
		
		$lSO = '';
		if ($post['limitstart'] != '') {
			$lSO = '&limitstart='.(int)$post['limitstart'];
		}
		
		$this->setRedirect( JRoute::_($this->url. $lSO, false), $msg );
	}
	
	function publish() {

		$post['id']			= JRequest::getVar( 'actionid', '', 'post', 'int', 0  );
		$post['limitstart']	= JRequest::getVar( 'limitstart', '', 'post', 'int', 0  );
		$model 				= $this->getModel('user');
		//$isOwnerCategory 	= 1;//$model->isOwnerCategoryImage((int)$this->_user->id, (int)$id);
		
		// USER RIGHT - Delete - - - - - - - - - - -
		// 2, 2 means that user access will be ignored in function getUserRight for display Delete button
		$user = JFactory::getUser();
		$rightDisplayDelete	= 0;
		$catAccess	= PhocaDownloadHelper::getCategoryAccessByFileId((int)$post['id']);
		
		if (!empty($catAccess)) {
			$rightDisplayDelete = PhocaDownloadHelper::getUserRight('deleteuserid', $catAccess->deleteuserid, 2, $user->authorisedLevels(), $user->get('id', 0), 0);
		}
		// - - - - - - - - - - - - - - - - - - - - - -	
	
		if ($rightDisplayDelete) {
			if(!$model->publish((int)$post['id'], 1)) {
			$msg = JText::_('COM_PHOCADOWNLOAD_ERROR_PUBLISHING_ITEM');
			} else {
			$msg = JText::_('COM_PHOCADOWNLOAD_SUCCESS_PUBLISHING_ITEM');
			} 
		} else {
			$app	= JFactory::getApplication();
			$app->redirect($this->loginUrl, $this->loginString);
			exit;
		}
		
		$lSO = '';
		if ($post['limitstart'] != '') {
			$lSO = '&limitstart='.(int)$post['limitstart'];
		}
		
		$this->setRedirect( JRoute::_($this->url. $lSO, false), $msg );
	}
	
	function delete() {

		$post['id']			= JRequest::getVar( 'actionid', '', 'post', 'int', 0  );
		$post['limitstart']	= JRequest::getVar( 'limitstart', '', 'post', 'int', 0  );
		$model 				= $this->getModel('user');
		//$isOwnerCategory 	= 1;//$model->isOwnerCategoryImage((int)$this->_user->id, (int)$id);
		
		// USER RIGHT - Delete - - - - - - - - - - -
		// 2, 2 means that user access will be ignored in function getUserRight for display Delete button
		$user = JFactory::getUser();
		$rightDisplayDelete	= 0;
		$catAccess	= PhocaDownloadHelper::getCategoryAccessByFileId((int)$post['id']);
		if (!empty($catAccess)) {
			$rightDisplayDelete = PhocaDownloadHelper::getUserRight('deleteuserid', $catAccess->deleteuserid, 2, $user->authorisedLevels(), $user->get('id', 0), 0);
		}
		// - - - - - - - - - - - - - - - - - - - - - -	
		
		if ($rightDisplayDelete) {
			if(!$model->delete((int)$post['id'])) {
			$msg = JText::_('COM_PHOCADOWNLOAD_ERROR_DELETING_ITEM');
			} else {
			$msg = JText::_('COM_PHOCADOWNLOAD_SUCCESS_DELETING_ITEM');
			} 
		} else {
			$app	= JFactory::getApplication();
			$app->redirect($this->loginUrl, $this->loginString);
			exit;
		}
		
		$lSO = '';
		if ($post['limitstart'] != '') {
			$lSO = '&limitstart='.(int)$post['limitstart'];
		}
		
		$this->setRedirect( JRoute::_($this->url. $lSO, false), $msg );
	}
}
?>