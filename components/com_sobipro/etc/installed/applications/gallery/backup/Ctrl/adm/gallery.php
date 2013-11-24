<?php
/**
 * ------------------------------------------------------------------------
 * Gallery Plugin For SobiPro
 * ------------------------------------------------------------------------
 * @copyright   Copyright (C) 2011-2012 Chartiermedia.com - All Rights Reserved.
 * @license     GNU/GPL, http://www.gnu.org/copyleft/gpl.html
 * @author:     Sebastien Chartier
 * @link:     http://www.chartiermedia.com
 * ------------------------------------------------------------------------
 *
 * @package	Joomla.Plugin
 * @subpackage  Gallery
 * @version     1.12
 * @since	1.7
 */

defined( 'SOBIPRO' ) || exit( 'Restricted access' );
SPLoader::loadController( 'controller' );

class SPGallery extends SPController
{
	/**
	 * @var string
	 */
	protected $_defTask = 'licenses';
	public function __construct() {}
	/**
	 */
	public function execute()
	{
		SPLang::load( 'SpApp.gallery' );
		$this->_task = strlen( $this->_task ) ? $this->_task : $this->_defTask;
		switch ( $this->_task ) {
			case 'licenses':
				$this->licenses();
				break;
			case 'license':
				$this->license();
				break;
			case 'saveLicense':
				$this->saveLicense();
				break;
			case 'ldelete':
				$this->delLicense();
				break;
		}

	}

	protected function delLicense()
	{
		$lic = SPRequest::int( 'lid', 0, 'get' );
		SPFactory::db()->delete( 'spdb_language', array( 'sKey' => 'license', 'oType' => 'app_gallery', 'id' => $lic ) );
		Sobi::Redirect( Sobi::Url( array( 'task' => 'gallery.licenses', 'out' => 'html' ) ) );
	}

	protected function saveLicense()
	{
		$title = SPRequest::string( 'license_title', null, false, 'post' );
		$txt = SPRequest::string( 'license_text', null, 2, 'post' );
		$lic = SPRequest::int( 'lic', 0, 'post' );
		if( !( $lic ) ) {
			$lic = SPFactory::db()->select( 'MAX( id )', 'spdb_language', array( 'sKey' => 'license', 'oType' => 'app_gallery' ) )->loadResult();
			$lic++;
		}
		SPFactory::db()->replace( 'spdb_language', array( 'license', $title, 0, Sobi::Lang(), 'app_gallery', 0, $lic, '', '', $txt ) );
		Sobi::Redirect( Sobi::Url( array( 'task' => 'gallery.licenses', 'out' => 'html', 'a' => 1 ) ) );
	}


	protected function licenses()
	{
		$lic = SPFactory::db()->select(
			array( 'sValue', 'language', 'explanation', 'id' ), 'spdb_language',
			array( 'sKey' => 'license', 'oType' => 'app_gallery', 'language' => array( Sobi::Lang(), SOBI_DEFLANG, 'en-GB' ) )
		)->loadAssocList( 'id' );
		$licenses = array();
		foreach ( $lic as $i => $l ) {
			$licenses[] = array(
				'id' => $l[ 'id' ],
				'title' => $l[ 'sValue' ],
				'text' => $l[ 'explanation' ],
				'url' => Sobi::Url( array( 'task' => 'gallery.license', 'lid' => $l[ 'id' ], 'out' => 'html' ) ),
				'durl' => Sobi::Url( array( 'task' => 'gallery.ldelete', 'lid' => $l[ 'id' ], 'out' => 'html' ) )
			);
		}
		$aurl = Sobi::Url( array( 'task' => 'gallery.license', 'out' => 'html' ) );
		$view =& SPFactory::View( 'view', true );
		$view->assign( $this->_task, 'task' );
		$view->assign( $licenses, 'licenses' );
		$view->assign( $aurl, 'addUrl' );
		$view->addHidden( $lic, 'lic' );
		$view->setTemplate( 'field.licenses' );
		$view->display();
	}

	protected function license()
	{
		$lid = SPRequest::int( 'lid', 0 );
		if( $lid ) {
			$lic = SPFactory::db()->select(
				array( 'sValue', 'language', 'explanation', 'id' ), 'spdb_language',
				array( 'sKey' => 'license', 'oType' => 'app_gallery', 'id' => $lid, 'language' => array( Sobi::Lang(), SOBI_DEFLANG, 'en-GB' ) )
			)->loadObject();
			$license = array( 'id' => $lic->id, 'title' => $lic->sValue, 'text' => $lic->explanation );
		}
		else {
			$license = array( 'id' => '', 'title' => '', 'text' => '' );
		}
		$task = 'gallery.saveLicense';
		$burl = Sobi::Url( array( 'task' => 'gallery.licenses', 'out' => 'html' ) );
		$view =& SPFactory::View( 'view', true );
		$raw = Sobi::Url( array( 'out' => 'raw' ), true );
		$raw = explode( '&', $raw );
		$raw = explode( '=', $raw[ 1 ] );
		$view->addHidden( $raw[ 1 ], $raw[ 0 ] );
		$view->loadConfig( 'field.edit' );
		$view->assign( $license, 'license' );
		$view->assign( $burl, 'backUrl' );
		$view->assign( $task, 'task' );
		$view->addHidden( $task, 'task' );
		$view->addHidden( $lid, 'lic' );
		$view->setTemplate( 'field.license' );
		$view->display();
	}
}
?>