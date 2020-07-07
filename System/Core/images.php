<?php
class Images extends Model{
    //----------------------------------    INIZIO RESIZING    -------------------------------//
    var $image;
    var $image_type;

    function load($filename) {

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
    function save($filename, $compression=75, $permissions=null) {

        if( $this->image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image,$filename,$compression);
        } elseif( $this->image_type == IMAGETYPE_GIF ) {

            imagegif($this->image,$filename);
        } elseif( $this->image_type == IMAGETYPE_PNG ) {

            imagepng($this->image,$filename);
        }
        if( $permissions != null) {
            chmod($filename,$permissions);
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
		if($this->getHeight() < $height)
    		return true;
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width,$height);
    }

    function resizeToWidth($width) {
    	if($this->getWidth() < $width)
    		return true;
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width,$height);
    }

    function scale($scale) {
        $width = $this->getWidth() * $scale/100;
        $height = $this->getheight() * $scale/100;
        $this->resize($width,$height);
    }

    function resizeTo($max){
        if($this->getWidth() > $this->getHeight())
            $this->resizeToWidth($max);
        else
            $this->resizeToHeight($max);
    }

    function resize($width,$height) {
        $new_image = imagecreatetruecolor($width, $height);
        if( $this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG ) {
					
            $current_transparent = imagecolortransparent($this->image);
			
            $palletsize = imagecolorstotal($this->image);
            if($current_transparent != -1 and $current_transparent < $palletsize) {
                $transparent_color = imagecolorsforindex($this->image, $current_transparent);
                $current_transparent = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($new_image, 0, 0, $current_transparent);
                imagecolortransparent($new_image, $current_transparent);
            } elseif( $this->image_type == IMAGETYPE_PNG) {
                imagealphablending($new_image, false);
                $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                imagefill($new_image, 0, 0, $color);
                imagesavealpha($new_image, true);
            }
        }
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());				
        $this->image = $new_image;
		
    }

    //----------------------------------    FINE RESIZING    -------------------------------//

	
    public function deleteImage($image_name, $gallery_id) {
        if(!$this->user->user_id()) return false;

        $dir = '../album/' . $gallery_id . '/';
        if(!is_dir($dir)) return false;

        if (file_exists ( $dir.$image_name ) and ! is_dir ( $dir.$image_name ))
            unlink ( $dir.$image_name );
        if (file_exists ( $dir.'thumb_'.$image_name ) and ! is_dir ( $dir.'thumb_'.$image_name ))
            unlink ( $dir.'thumb_'.$image_name );
        return true;
    }

    public function uploadTemp($type = '') {
        if(!$this->user->user_id()) return false;

        require_once ('system/class/qqUploader.php');
        $allowedExtensions = array ("jpeg", "jpg", "pjpeg", "png", "x-png" , "gif");
        $uploader = new qqFileUploader ( $type, $allowedExtensions );

        $temp_dir = 'tmp/';

        if (! is_dir ( $temp_dir ))
            @mkdir ( $temp_dir, 0777, true );

        if (!is_dir($temp_dir) || !is_writable($temp_dir)) {
            return array(
                'jsonrpc' => "2.0",
                'error' => array('code' => 100, 'message' => 'Impossibile accedere alla cartella temporanea'),
                'id' => "id"
            );
        }

        $result = $uploader->handleUpload ( $temp_dir );
		
        $expire_time = 60 * 3; // Durata delle immagini da conservare - 3 ore

        foreach ( glob ( $temp_dir . '*.*' ) as $filename ) {
            $fileCreationTime = filectime ( $filename );
            $fileAge = time () - $fileCreationTime;
            if ($fileAge > ($expire_time * 60)) {
                @unlink ( $filename );
            }
        }

        if (isset ( $result ['file'] ) and file_exists ( $result ['file'] )) {

            $result['filename'] = str_replace($temp_dir,'', $result['file']);           

            return array(
                'jsonrpc' => "2.0",
                'error' => null,
                'result' => $result,
                'id' => "id"
            );
        }

        return array(
            'jsonrpc' => "2.0",
            'error' => array('code' => 100, 'message' => $result['error']),
            'id' => "id"
        );
    }

    public static function removeTemp($filename){
        //if(!$this->user->isLogged())    return false;

        $filename = str_replace(array('\\', '/'), '',$filename);

        $targetFolder = 'tmp/';
        if(file_exists($targetFolder.$filename)){
            unlink($targetFolder.$filename);
            if(file_exists($targetFolder.'thumb_'.$filename))
                unlink($targetFolder.'thumb_'.$filename);
        }
        return true;
    }
}

