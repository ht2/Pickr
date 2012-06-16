<?php
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
* 
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 2 
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
*/
 
class SimpleImage {
   
   var $image;
   var $image_type;
   var $filename;
 
   function load($filename) {
	   $this->filename = $filename;
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
		 	imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
         	imagepng($this->image,$filename);
      } 
   }
   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }   
   }
   function getWidth() {
      return imagesx($this->image);
   }
   function getHeight() {
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }
   function resize($width,$height) {
     	$new_image = imagecreatetruecolor($width, $height);
		imagealphablending($new_image, false);
	 	imagesavealpha($new_image,true);
 		$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
 		imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
      	imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      	$this->image = $new_image;   
   }    
   
   function rescaleImage( $maxWidth, $maxHeight)
   {
	   	// Constraints
		list($width, $height) = getimagesize($this->filename);
		$ratioh = $maxHeight/$height;
		$ratiow = $maxWidth/$width;
		$ratio = min($ratioh, $ratiow);
		// New dimensions
		$width = intval($ratio*$width);
		$height = intval($ratio*$height); 
		
		$this->resize( $width, $height );
   }
   
   function rescaleImageMax( $maxWidth, $maxHeight)
   {
	   	// Constraints
		list($width, $height) = getimagesize($this->filename);
		$ratioh = $maxHeight/$height;
		$ratiow = $maxWidth/$width;
		$ratio = max($ratioh, $ratiow);
		// New dimensions
		$width = intval($ratio*$width);
		$height = intval($ratio*$height); 
		
		$this->resize( $width, $height );
   }
   
   
   
   function checkToRescale( $maxWidth, $maxHeight )
   {
		list($width, $height) = getimagesize($this->filename);
		if( $width > $maxWidth || $height > $maxHeight ) $this->rescaleImage( $maxWidth, $maxHeight );			
   }


}
?>