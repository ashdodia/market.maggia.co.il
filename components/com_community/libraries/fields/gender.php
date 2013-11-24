<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once (COMMUNITY_COM_PATH.'/libraries/fields/profilefield.php');
class CFieldsGender extends CProfileField
{

	/**
	 * Method to format the specified value for text type
	 **/
	public function getFieldData( $field )
	{
		$options = array( "male" => "COM_COMMUNITY_MALE", "female" => "COM_COMMUNITY_FEMALE" );
		$value = strtolower($field['value']);
		return JText::_( $options[$value] );
	}

	public function getFieldHTML( $field , $required )
	{
		$html				= '';
		$selectedElement	= 0;
		$class				= ($field->required == 1) ? ' required validate-custom-radio ' : '';
		$style 				= ' style="margin: 0 5px 0 0;' .$this->getStyle() . '" ';

		// Gender contain only male and female
		$options = array( "COM_COMMUNITY_MALE"=>"male", "COM_COMMUNITY_FEMALE" =>"female" );

		$cnt = 0;
		//CFactory::load( 'helpers' , 'string' );
		$class	= !empty( $field->tips ) ? 'jomNameTips tipRight' : '';
		$html	.= '<div class="' . $class . '" style="display: inline-block;" title="' . CStringHelper::escape( JText::_( $field->tips ) ). '">';

		foreach( $options as $key => $val )
		{
			$selected	= ( $val == $field->value ) ? ' checked="checked"' : '';

			$html 	.= '<label class="lblradio-block">';
			$html	.= '<input type="radio" name="field' . $field->id . '" value="' . $val . '"' . $selected . '  class="radio" '.$style.' />';
			$html	.= JText::_( $key ) . '</label>';
		}

		$html   .= '<span id="errfield'.$field->id.'msg" style="display: none;">&nbsp;</span>';
		$html	.= '</div>';

		return $html;
	}

	public function isValid( $value , $required )
	{
		if( $required && empty($value))
		{
			return false;
		}
		return true;
	}
}