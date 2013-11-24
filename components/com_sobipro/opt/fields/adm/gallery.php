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
SPLoader::loadClass( 'opt.fields.gallery' );


class SPField_GalleryAdm extends SPField_Gallery implements SPFieldInterface
{
	public function onFieldEdit( &$view )
	{
		SPLang::load( 'SpApp.gallery' );
	}

	public function save( &$attr )
	{
		$attr[ 'maxSize' ] = $attr[ 'maxSize' ] * SPRequest::int( 'sizeMulti', 1, 'post' );
		$attr[ 'allowedExt' ] = explode( ',', $attr[ 'allowedExt' ] );
		SPLang::load( 'SpApp.gallery' );
		$maxSize = ( int ) ini_get( 'upload_max_filesize' );
		$maxSizeUnit = substr( ini_get( 'upload_max_filesize' ), -1 );
		if( $maxSize && $maxSize < 0 ) {
			$multi = array( 'b' => 1, 'k' => 1024, 'm' => 1048576, 'g' => 1073741824 );
			$maxSize = $maxSize * $multi[ strtolower( $maxSizeUnit ) ];
			if( $maxSize < $attr[ 'maxSize' ] ) {
				SPMainFrame::msg( Sobi::Txt( 'FIELD_GAL_LIMIT_HIGHER_THAN_PHP' ), SPC::ERROR_MSG );
				$attr[ 'maxSize' ] = $maxSize - 1;
			}
		}
		if( count( $attr[ 'allowedExt' ] ) ) {
			foreach ( $attr[ 'allowedExt' ] as $i => $ext ) {
				$attr[ 'allowedExt' ][ $i ] = trim( $ext );
			}
		}
		$myAttr = $this->getAttr();
		$properties = array();
		if( count( $myAttr ) ) {
			foreach ( $myAttr as $property ) {
				$properties[ $property ] = isset( $attr[ $property ] ) ? ( $attr[ $property ] ) : null;
			}
		}
		$attr[ 'params' ] = $properties;
	}
}