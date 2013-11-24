<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSFormModelRSForm extends JModel
{
	var $params;
	
	function __construct()
	{
		parent::__construct();
		
		$app 			= JFactory::getApplication();
		$this->params 	= $app->getParams('com_rsform');
	}

	function getFormId()
	{		
		if (!$formId = JRequest::getInt('formId', 0, 'get'))
			$formId = $this->params->get('formId');
		
		return $formId;
	}
	
	function getParams()
	{
		return $this->params;
	}
}