<?php
/*------------------------------------------------------------------------
# TZ Content Slider
# ------------------------------------------------------------------------
# Author    ThemeZart http://www.ThemeZart.com
# Copyright (C) 2010 - 2012 ThemeZart.com. All Rights Reserved.
# @license - GNU/GPL V2 for PHP files. CSS / JS are Copyrighted Commercial
# Websites: http://www.ThemeZart.com
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filter.output');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);

class modTzContentSliderCommonHelper {

	public static function cText($params, $text, $limit, $type=0) {
		$prepare_content = $params->get('prepare_content');
		//function to cut text
		if($prepare_content){
			$newtext 					= preg_replace('/<img[^>]+\>/i', "", $text);
		}else{
			$newtext = $text;
		}
		
		if ($limit==0) {//no limit
			if($prepare_content){
				$allowed_tags 		= '<blockquote><p><b><i><a><small><h1><h2><h3><h4><h5><h6><sup><sub><em><strong><u><br>';
				$newtext 				= strip_tags( $newtext, $allowed_tags );
			}
				
		} else {
			if ($type==1) {//character lmit
				$newtext 			= JFilterOutput::cleanText($newtext);
				$sep  			= (strlen($newtext)>$limit) ? '...' : '';
				$newtext 			= utf8_substr($newtext,0,$limit) . $sep;		
			} else {//word limit
				$newtext 			= JFilterOutput::cleanText($newtext);
				$newtext 			= explode(' ',$newtext);
				$sep 			= (count($newtext)>$limit) ? '...' : '';
				$newtext			= implode(' ', array_slice($newtext,0,$limit)) . $sep;		
			}		
		}
		$text 				= $newtext;	
		return $text;
	}
	
	public static function thumb($image, $width, $height, $ratio=false, $uniqid) {
			
		// remove any / that begins the path
		if (substr($image, 0 , 1) == '/') $image = substr($image, 1);
		
		// create a thumb filename
		$file_dir = dirname($image);
		$thumb_dir = $file_dir . DS . "tzslider_thumbs";
		if (!JFolder::exists($thumb_dir)) {
			JFolder::create($thumb_dir);
		}
		$file_name = JFile::stripExt(basename($image));
		$file_ext = JFile::getExt($image);
		$thumb_path = $thumb_dir . DS. $file_name . '_' . $uniqid . "_thumb." . $file_ext;

		// check to see if this file exists, if so we don't need to create it
		if (function_exists("gd_info")) {
			
			//Check existing thumbnails dimensions
			if (file_exists($thumb_path)) {
				$size = GetImageSize( $thumb_path );
				$currentWidth=$size[0];
				$currentHeight=$size[1];
			}
			
			//Creating thumbnails		
			if (!file_exists($thumb_path) || $currentWidth!=$width || $currentHeight!=$height ) {
				modTzContentSliderCommonHelper::crop($image, $width, $height, $ratio, $thumb_path);
			}
		}
			
		return str_replace("\\","/",$thumb_path);	

	
	}
	
	protected function crop($image_to_resize, $new_width, $new_height, $ratio, $path)
	{
		if( !file_exists( $image_to_resize ) )
		{
			exit( "File " . $image_to_resize . " does not exist." );
		}
		
		$info = GetImageSize( $image_to_resize );
		
		if( empty( $info ) )
		{
			exit( "The file " . $image_to_resize . " doesn't seem to be an image." );
		}
		
		$width = $info[ 0 ];
		$height = $info[ 1 ];
		$mime = $info[ 'mime' ];/* Keep Aspect Ratio? */
		if( $ratio )
		{
			$thumb = ( $new_width < $width && $new_height < $height ) ? true : false;// Thumbnail
			$bigger_image = ( $new_width > $width || $new_height > $height ) ? true : false;// Bigger Image
			if( $thumb )
			{
				
				if( $new_width >= $new_height )
				{
					$x = ( $width / $new_width );
					$new_height = ( $height / $x );
				}
				elseif( $new_height >= $new_width )
				{
					$x = ( $height / $new_height );
					$new_width = ( $width / $x );
				}
			}
			elseif( $bigger_image )
			{
				
				if( $new_width >= $width )
				{
					$x = ( $new_width / $width );
					$new_height = ( $height * $x );
				}
				elseif( $new_height >= $height )
				{
					$x = ( $new_height / $height );
					$new_width = ( $width * $x );
				}
			}
		}// What sort of image?
		$type = substr( strrchr( $mime, '/' ), 1 );
		
		switch( $type )
		{
		case 'jpeg':
			$image_create_func = 'ImageCreateFromJPEG';
			$image_save_func = 'ImageJPEG';
			$new_image_ext = 'jpg';
			break;
		case 'png':
			$image_create_func = 'ImageCreateFromPNG';
			$image_save_func = 'ImagePNG';
			$new_image_ext = 'png';
			break;
		case 'bmp':
			$image_create_func = 'ImageCreateFromBMP';
			$image_save_func = 'ImageBMP';
			$new_image_ext = 'bmp';
			break;
		case 'gif':
			$image_create_func = 'ImageCreateFromGIF';
			$image_save_func = 'ImageGIF';
			$new_image_ext = 'gif';
			break;
		case 'vnd.wap.wbmp':
			$image_create_func = 'ImageCreateFromWBMP';
			$image_save_func = 'ImageWBMP';
			$new_image_ext = 'bmp';
			break;
		case 'xbm':
			$image_create_func = 'ImageCreateFromXBM';
			$image_save_func = 'ImageXBM';
			$new_image_ext = 'xbm';
			break;
			default: $image_create_func = 'ImageCreateFromJPEG';
			$image_save_func = 'ImageJPEG';
			$new_image_ext = 'jpg';
		}// New Image
		
		$image_c = ImageCreateTrueColor( $new_width, $new_height );
		
		if ($type=='png') {
			imagealphablending($image_c, false);
			imagesavealpha($image_c, true);
		}
		$new_image = $image_create_func( $image_to_resize );
		if ($type=='png') {
			imagealphablending($new_image, true);
		}
		ImageCopyResampled( $image_c, $new_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
		
		$process = $image_save_func( $image_c, $path );
		return array( 'result' => $process, 'new_file_path' => $path );
	}
}