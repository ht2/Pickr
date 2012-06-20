<?php

class TemplateProxy extends Proxy
{
	const NAME = "TemplateProxy";
	
	public function __construct()
	{
		parent::__construct( TemplateProxy::NAME, new VO() );
		$this->template = new Template();
	}
	
	public static function loadFile( $url )
	{
		$file = file_get_contents( $url ) or die("error loading file");
		return $file;
	}
	
	public function createSortableTable( $header_columns, $body_rows, $table_ident="", $header_options = array(), $class='sortable' )
	{
		$table_id = ($table_ident=="") ? "" : "id='$table_ident'";
		
		//HEAD
		$html = br("<table class='$class' ".$table_id." cellspacing='1'>");
		$html.= br("<thead>");
		$html.= br("<tr>");
        $i = 0;
		foreach( $header_columns as $hc )
		{
            $width = "";
            $align = "";
            if( isset($header_options[$i]) ){
                $options = $header_options[$i];
                if( isset($options['width'])){
                   $width = 'width="'.$options['width'].'"'; 
                }
                if( isset($options['align'])){
                    $align = 'align="'.$options['align'].'"'; 
                }
            }
			$html.= br("<th $width $align>$hc</th>");	
            $i++;
		}	
		$html.= br("</tr>");
		$html.= br("</thead>");
		
		//BODY
		$html.= br("<tbody>");		
        
            $j=0;
		foreach( $body_rows as $row )
		{
			$html.= br("<tr>");
            
            $j=0;
			foreach( $row as $col ) {
                
                $width = "";
                $align = "";
                 if( isset($header_options[$j]) ){
                    $options = $header_options[$j];
                    if( isset($options['width'])){
                        $width = 'width="'.$options['width'].'"'; 
                    }
                    if( isset($options['align'])){
                        $align = 'align="'.$options['align'].'"'; 
                    }
                }
                $html.= br("<td $width $align>$col</td>"); 
                $j++;
            }
			$html.= br("</tr>");
           
		}		
		$html.= br("</tbody>");
		$html.= br("</table>");		
		return $html;
	}
    
    public function createOptions( $options, $selected=NULL){
        
        $html = "";
        foreach( $options as $o ){
            $value  = $o[0];
            $name   = $o[1];
            $extra  = ($value == $selected) ? "selected='selected'" : "";
            
            $html .= "<option value='$value' $extra>$name</option>";
        }
        
        return $html;
    }
    
    public function YN( $val ){
        return (intval($val)) ? "Yes" : "No";
    }
    
	public function maxUploadSize(){
		return 20971520; //20Mb
	}
	
    public function maxImageSize(){
		return 2097152; //2Mb
	}
	
	public function validUploadsExt()
	{
		return "pdf|doc|docx|xls|xlsx|txt|mp3|wav|mp4|mov|flv|wmv|avi" . "|" . $this->validImageExt();
	}
	
	public function validImageExt()
	{
		return "jpg|jpeg|png|gif|bmp";
	}
    
    public function handleImageUpload( $filename, $field_name='uploadedimage' ){
        $valid_exts 	= explode( "|", $this->facade->retrieveProxy( TemplateProxy::NAME )->validImageExt() );
        $max_file_size	= $this->facade->retrieveProxy( TemplateProxy::NAME )->maxImageSize();

        $upload_location 	= HOST."/view/uploads/images/";
        $pathinfo 			= pathinfo( $_FILES[$field_name]['name']);
        $error				= $_FILES[$field_name]['error'];
        $size				= $_FILES[$field_name]['size'];
        $ext 				= strtolower($pathinfo['extension']);
    
        //Check file ext and that not bigger than 20MB
        if( in_array( $ext, $valid_exts ) && $error==0 && $size<=$max_file_size ){

            $uploadfilename = $_FILES[$field_name]['name'];
            $orig_file_name = substr($uploadfilename, 0, strrpos($uploadfilename,".") );
            $orig_file_name = cleanForShortURL( $orig_file_name );
            $fileext = strtolower(substr($uploadfilename,strrpos($uploadfilename,".")+1));										
            $fullfilename = $filename . "." . $fileext;

            if( !file_exists($upload_location) ){  
                mkdir( $upload_location );
            }					

            move_uploaded_file( $_FILES[$field_name]['tmp_name'], $upload_location.$fullfilename );
            return $fullfilename;
        } else {
            return false;
        }
    }
    
    public function handleFileUpload(){
        $valid_exts 	= explode( "|", $this->facade->retrieveProxy( TemplateProxy::NAME )->validUploadsExt() );
        $max_file_size	= $this->facade->retrieveProxy( TemplateProxy::NAME )->maxUploadSize();

        $upload_location 	= HOST."/uploads/resources/";
        $pathinfo 			= pathinfo( $_FILES['uploadedfile']['name']);
        $error				= $_FILES['uploadedfile']['error'];
        $size				= $_FILES['uploadedfile']['size'];
        $ext 				= strtolower($pathinfo['extension']);
    
        //Check file ext and that not bigger than 20MB
        if( in_array( $ext, $valid_exts ) && $error==0 && $size<=$max_file_size ){

            $uploadfilename = $_FILES['uploadedfile']['name'];
            $orig_file_name = substr($uploadfilename, 0, strrpos($uploadfilename,".") );
            $orig_file_name = cleanForShortURL( $orig_file_name );
            $fileext = strtolower(substr($uploadfilename,strrpos($uploadfilename,".")+1));										
            $fullfilename = $orig_file_name . "_" . time() . "." . $fileext;

            if( !file_exists($upload_location) ){  
                mkdir( $upload_location );
            }					

            move_uploaded_file( $_FILES['uploadedfile']['tmp_name'], $upload_location.$fullfilename );
            return $fullfilename;
        } else {
            return false;
        }
    }

	public function vo()
	{
		return $this->getData();
	}
}
?>
