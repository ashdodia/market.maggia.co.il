<?php
/**
 * @version: $Id: url.php 1883 2011-09-16 17:44:53Z Radek Suski $
 * @package: SobiPro Library
 * ===================================================
 * @author
 * Name: Sigrid Suski & Radek Suski, Sigsiu.NET GmbH
 * Email: sobi[at]sigsiu.net
 * Url: http://www.Sigsiu.NET
 * ===================================================
 * @copyright Copyright (C) 2006 - 2011 Sigsiu.NET GmbH (http://www.sigsiu.net). All rights reserved.
 * @license see http://www.gnu.org/licenses/lgpl.html GNU/LGPL Version 3.
 * You can use, redistribute this file and/or modify it under the terms of the GNU Lesser General Public License version 3
 * ===================================================
 * $Date: 2011-09-16 19:44:53 +0200 (Fri, 16 Sep 2011) $
 * $Revision: 1883 $
 * $Author: Radek Suski $
 * File location: components/com_sobipro/opt/fields/url.php $
 */
defined( 'SOBIPRO' ) || exit( 'Restricted access' );
SPLoader::loadClass( 'opt.fields.inbox' );

/**
 * @author Radek Suski
 * @version 1.0
 * @created 14-Sep-2009 11:36:48
 */
class SPField_Extra extends SPField_Inbox implements SPFieldInterface
{
	/**
	 * @var bool
	 */
	protected $ownLabel =  true;
	/**
	 * @var int
	 */
	protected $labelWidth =  350;
	/**
	 * @var string
	 */
	protected $labelsLabel = "Website Title";
	/**
	 * @var int
	 */
	protected $labelMaxLength =  150;
	/**
	 * @var int
	 */
	protected $maxLength =  150;
	/**
	 * @var int
	 */
	protected $width =  350;
	/**
	 * @var string
	 */
	protected $cssClass = "";
	/**
	 * @var bool
	 */
	protected $validateUrl =  false;
	/**
	 * @var array
	 */
	protected $allowedProtocols =  array( 'http', 'https', 'ftp' );
	/**
	 * @var bool
	 */
	protected $newWindow =  true;

	/**
	 * Shows the field in the edit entry or add entry form
	 * @param bool $return return or display directly
	 * @return string
	 */
	public function field( $return = false )
	{
		if( !( $this->enabled ) ) {
			return false;
		}
		$field = null;
		$fdata = Sobi::Reg( 'editcache' );
		if( $fdata && is_array( $fdata ) ) {
			$raw = $this->fromCache( $fdata );
		}
		else {
			$raw = SPConfig::unserialize( $this->getRaw() );
		}
		$params[ 'id' ] = $this->nid.'_name';
		$field .= '<div>' . "שם התוספת - " . SPHtml_Input::text( $this->nid.'_name', ( ( is_array( $raw ) && isset( $raw[ 'name' ] ) ) ? $raw[ 'name' ] : null ), $params ) .'</div> <hr>';
		$params[ 'id' ] = $this->nid.'_opt1_desc';
		$field .= "תאור אופציה 1 - " . SPHtml_Input::text( $this->nid.'_opt1_desc', ( ( is_array( $raw ) && isset( $raw[ 'opt1_desc' ] ) ) ? $raw[ 'opt1_desc' ] : null ), $params ) .'</br>';
		$params[ 'id' ] = $this->nid.'_opt1_price';
		$field .= "מחיר אופציה 1 - " . SPHtml_Input::text( $this->nid.'_opt1_price', ( ( is_array( $raw ) && isset( $raw[ 'opt1_price' ] ) ) ? $raw[ 'opt1_price' ] : null ), $params ) .'</br> <hr>';
		$params[ 'id' ] = $this->nid.'_opt2_desc';
		$field .= "תאור אופציה 2 - " . SPHtml_Input::text( $this->nid.'_opt2_desc', ( ( is_array( $raw ) && isset( $raw[ 'opt2_desc' ] ) ) ? $raw[ 'opt2_desc' ] : null ), $params ) .'</br>';
		$params[ 'id' ] = $this->nid.'_opt2_price';
		$field .= "מחיר אופציה 2 - " . SPHtml_Input::text( $this->nid.'_opt2_price', ( ( is_array( $raw ) && isset( $raw[ 'opt2_price' ] ) ) ? $raw[ 'opt2_price' ] : null ), $params ) .'</br> <hr>';
		$params[ 'id' ] = $this->nid.'_opt3_desc';
		$field .= "תאור אופציה 3 - " . SPHtml_Input::text( $this->nid.'_opt3_desc', ( ( is_array( $raw ) && isset( $raw[ 'opt3_desc' ] ) ) ? $raw[ 'opt3_desc' ] : null ), $params ) .'</br>';
		$params[ 'id' ] = $this->nid.'_opt3_price';
		$field .= "מחיר אופציה 3 - " . SPHtml_Input::text( $this->nid.'_opt3_price', ( ( is_array( $raw ) && isset( $raw[ 'opt3_price' ] ) ) ? $raw[ 'opt3_price' ] : null ), $params ) .'</br>';
		if( !$return ) {
			echo $field;
		}
		else {
			return $field;
		}
	}

	private function fromCache( $cache )
	{
		$data = array();
		if( isset( $cache ) && isset( $cache[ $this->nid.'_name' ] ) ) {
			$data[ 'name' ] = $cache[ $this->nid.'_name' ];
		}
		if( isset( $cache ) && isset( $cache[ $this->nid.'_opt1_desc' ] ) ) {
			$data[ 'opt1_desc' ] = $cache[ $this->nid.'_opt1_desc' ];
		}
		if( isset( $cache ) && isset( $cache[ $this->nid.'_opt1_price' ] ) ) {
			$data[ 'opt1_price' ] = $cache[ $this->nid.'_opt1_price' ];
		}
		if( isset( $cache ) && isset( $cache[ $this->nid.'_opt2_desc' ] ) ) {
			$data[ 'opt2_desc' ] = $cache[ $this->nid.'_opt2_desc' ];
		}
		if( isset( $cache ) && isset( $cache[ $this->nid.'_opt2_price' ] ) ) {
			$data[ 'opt2_price' ] = $cache[ $this->nid.'_opt2_price' ];
		}
		if( isset( $cache ) && isset( $cache[ $this->nid.'_opt3_desc' ] ) ) {
			$data[ 'opt3_desc' ] = $cache[ $this->nid.'_opt3_desc' ];
		}
		if( isset( $cache ) && isset( $cache[ $this->nid.'_opt3_price' ] ) ) {
			$data[ 'opt3_price' ] = $cache[ $this->nid.'_opt3_price' ];
		}
		return $data;
	}

	/**
	 * Returns the parameter list
	 * @return array
	 */
	protected function getAttr()
	{
		return array( 'ownLabel', 'labelWidth', 'labelMaxLength', 'labelsLabel', 'validateUrl', 'allowedProtocols', 'newWindow', 'maxLength', 'width' );
	}

	/**
	 * @return array
	 */
	public function struct()
	{
		$data = SPConfig::unserialize( $this->getRaw() );
		if( isset( $data[ 'name' ] ) && strlen( $data[ 'name' ] ) ) {
			$this->cssClass = strlen( $this->cssClass ) ? $this->cssClass : 'spFieldsData';
			$this->cssClass = $this->cssClass.' '.$this->nid;
			$this->cleanCss();
			if( strlen( $data[ 'name' ] ) ) {
				$attributes = array( 'name' => $data[ 'name' ], 'class' => $this->cssClass);
				$attributes[ 'opt1_desc' ] = $data[ 'opt1_desc' ];
				$attributes[ 'opt1_price' ] = $data[ 'opt1_price' ];
				$attributes[ 'opt2_desc' ] = $data[ 'opt2_desc' ];
				$attributes[ 'opt2_price' ] = $data[ 'opt2_price' ];
				$attributes[ 'opt3_desc' ] = $data[ 'opt3_desc' ];
				$attributes[ 'opt3_price' ] = $data[ 'opt3_price' ];
				$data = array(
					'_complex' => 1,
					'_data' => SPLang::clean( $data[ 'name' ] ),
					'_attributes' => $attributes
				);
				return array (
					'_complex' => 1,
					'_data' => array( 'a' => $data ),
					'_attributes' => array( 'lang' => Sobi::Lang(false), 'class' => $this->cssClass )
				);
			}
		}
	}

	public function cleanData( $html )
	{
		$data = SPConfig::unserialize( $this->getRaw() );
		$url = null;
		if( isset( $data[ 'url' ] ) && strlen( $data[ 'url' ] ) ) {
			if( $data[ 'protocol' ] == 'relative' ) {
				$url = Sobi::Cfg( 'live_site' ).$data[ 'url' ];
			}
			else {
				$url = $data[ 'protocol' ].'://'.$data[ 'url' ];
			}
		}
		return $url;
	}

	/**
	 * Gets the data for a field, verify it and pre-save it.
	 * @param SPEntry $entry
	 * @param string $tsid
	 * @param string $request
	 * @return array
	 */
	public function submit( &$entry, $tsid = null, $request = 'POST' )
	{
		if( count( $this->verify( $entry, SPFactory::db(), $request ) ) ) {
			return SPRequest::search( $this->nid, $request );
		}
		else {
			return array();
		}
	}

	/**
	 * @param SPEntry $entry
	 * @param SPdb $db
	 * @param string $request
	 * @return array
	 */
	private function verify( $entry, &$db, $request )
	{
		$save = array();
		$save[ 'name' ] = SPRequest::raw( $this->nid.'_name', null, $request );
		$save[ 'opt1_desc' ] = SPRequest::raw( $this->nid.'_opt1_desc', null, $request );
		$save[ 'opt1_price' ] = SPRequest::raw( $this->nid.'_opt1_price', null, $request );
		$save[ 'opt2_desc' ] = SPRequest::raw( $this->nid.'_opt2_desc', null, $request );
		$save[ 'opt2_price' ] = SPRequest::raw( $this->nid.'_opt2_price', null, $request );
		$save[ 'opt3_desc' ] = SPRequest::raw( $this->nid.'_opt3_desc', null, $request );
		$save[ 'opt3_price' ] = SPRequest::raw( $this->nid.'_opt3_price', null, $request );
		$data = SPRequest::raw( $this->nid.'_name', null, $request );
		$dexs = strlen( $data );
		/* check if it was required */
		if( $this->required && !( $dexs ) ) {
			throw new SPException( SPLang::e( 'FIELD_REQUIRED_ERR', $this->name ) );
		}
		/* check if there was a filter */
		if( $this->filter && $dexs ) {
			$registry =& SPFactory::registry();
			$registry->loadDBSection( 'fields_filter' );
			$filters = $registry->get( 'fields_filter' );
			$filter = isset( $filters[ $this->filter ] ) ? $filters[ $this->filter ] : null;
			if( !( count( $filter ) ) ) {
				throw new SPException( SPLang::e( 'FIELD_FILTER_ERR', $this->filter ) );
			}
			else {
				if( !( preg_match( base64_decode( $filter[ 'params' ] ), $data ) ) ) {
					throw new SPException( str_replace( '$field', $this->name, SPLang::e( $filter[ 'description' ] ) ) );
				}
			}
		}
		/* check if there was an adminField */
		if( $this->adminField && $dexs ) {
			if( !( Sobi:: Can( 'entry.adm_fields.edit' ) ) ) {
				throw new SPException( SPLang::e( 'FIELD_NOT_AUTH', $this->name ) );
			}
		}
		/* check if it was free */
		if( !( $this->isFree ) && $this->fee && $dexs ) {
			SPFactory::payment()->add( $this->fee, $this->name, $entry->get( 'id' ), $this->fid );
		}
		/* check if it should contains unique data */
		if( $this->uniqueData && $dexs ) {
			$matches = $this->searchData( $data, Sobi::Reg( 'current_section' ) );
			if( count( $matches ) > 1 || ( ( count( $matches ) == 1 ) && ( $matches[ 0 ] != $entry->get( 'id' ) ) ) ) {
				throw new SPException( SPLang::e( 'FIELD_NOT_UNIQUE', $this->name ) );
			}
		}
		/* check if it was editLimit */
		if( $this->editLimit == 0 && !( Sobi::Can( 'entry.adm_fields.edit' ) ) && $dexs ) {
			throw new SPException( SPLang::e( 'FIELD_NOT_AUTH_EXP', $this->name ) );
		}
		/* check if it was editable */
		if( !( $this->editable ) && !( Sobi::Can( 'entry.adm_fields.edit' ) ) && $dexs && $entry->get( 'version' ) > 1 ) {
			throw new SPException( SPLang::e( 'FIELD_NOT_AUTH_NOT_ED', $this->name ) );
		}
		if( !( $dexs ) ) {
			$save = null;
		}
		return $save;
	}

	/**
	 * Gets the data for a field and save it in the database
	 * @param SPEntry $entry
	 * @return bool
	 */
	public function saveData( &$entry, $request = 'POST' )
	{
		if( !( $this->enabled ) ) {
			return false;
		}

		/* @var SPdb $db */
		$db =& SPFactory::db();
		$save = $this->verify( $entry, $db, $request );

		$time = SPRequest::now();
		$IP = SPRequest::ip( 'REMOTE_ADDR', 0, 'SERVER' );
		$uid = Sobi::My( 'id' );

		/* if we are here, we can save these data */
		/* collect the needed params */
		$params = array();
		$params[ 'publishUp' ] = $entry->get( 'publishUp' );
		$params[ 'publishDown' ] = $entry->get( 'publishDown' );
		$params[ 'fid' ] = $this->fid;
		$params[ 'sid' ] = $entry->get( 'id' );
		$params[ 'section' ] = Sobi::Reg( 'current_section' );
		$params[ 'lang' ] = Sobi::Lang();
		$params[ 'enabled' ] = $entry->get( 'state' );
		$params[ 'baseData' ] = $db->escape( SPConfig::serialize( $save ) );
		$params[ 'approved' ] = $entry->get( 'approved' );
		$params[ 'confirmed' ] = $entry->get( 'confirmed' );
		/* if it is the first version, it is new entry */
		if( $entry->get( 'version' ) == 1 ) {
			$params[ 'createdTime' ] = $time;
			$params[ 'createdBy' ] = $uid;
			$params[ 'createdIP' ] = $IP;
		}
		$params[ 'updatedTime' ] = $time;
		$params[ 'updatedBy' ] = $uid;
		$params[ 'updatedIP' ] = $IP;
		$params[ 'copy' ] = !( $entry->get( 'approved' ) );
		if( Sobi::My( 'id' ) == $entry->get( 'owner' ) ) {
			--$this->editLimit;
		}
		$params[ 'editLimit' ] = $this->editLimit;

		/* save it */
		try {
			/* Notices:
			 * If it was new entry - insert
			 * If it was an edit and the field wasn't filled before - insert
			 * If it was an edit and the field was filled before - update
			 *     " ... " and changes are not autopublish it should be insert of the copy .... but
			 * " ... " if a copy already exist it is update again
			 * */
			$db->insertUpdate( 'spdb_field_data', $params );
		}
		catch ( SPException $x ) {
			Sobi::Error( __CLASS__, SPLang::e( 'CANNOT_SAVE_DATA', $x->getMessage() ), SPC::WARNING, 0, __LINE__, __FILE__ );
		}

		/* if it wasn't edited in the default language, we have to try to insert it also for def lang */
		if( Sobi::Lang() != Sobi::DefLang() ) {
			$params[ 'lang' ] = Sobi::DefLang();
			try {
				$db->insert( 'spdb_field_data', $params, true, true );
			}
			catch ( SPException $x ) {
				Sobi::Error( __CLASS__, SPLang::e( 'CANNOT_SAVE_DATA', $x->getMessage() ), SPC::WARNING, 0, __LINE__, __FILE__ );
			}
		}
	}
}
?>