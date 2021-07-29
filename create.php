<?php
function Deleting($name, $result, $png){
    unlink($name);
    imagepng($result, __DIR__ . '/uploads' . $png);
    imagedestroy($result);
}
function Text($text, $font_size,$font){
            $ret = "";

            $arr = explode(' ', $text);
            $wrp = 0;

            foreach ( $arr as $word ){

            $teststring = $ret.' '.$word;
            $testbox = imagettfbbox($font_size, 0, $font, $teststring);
            if ( $testbox[2] > 350 ){
              $ret.="<br>".$word;
              $wrp=$wrp+1;
            } else {
              $ret.=" ".$word;
            }

            }
            $ret = $text;
            return $text;
}
//Submit button is activated
if(isset($_POST['submit'])){
    //Validate the uploaded picture
    if(isset($_FILES['picture'])&& preg_match('/^image/', $_FILES['picture']['type'])){

        //Saving the uploaded picture
        $dir = 'uploads/';
        $filename = $_FILES['picture']['tmp_name'];
        $name = $dir . time() . $_FILES['picture']['name'];
        if(move_uploaded_file($filename, $name)){
            //Taking the uploaded image
            $img = imagecreatefromstring(file_get_contents($name));
            if($img === false){
                echo "Some problems...";
            }
            //Scaling
            list($width_orig, $height_orig) = getimagesize($name);
            $width = 350;
            $height = 350;
            $ratio_orig = $width_orig/$height_orig;
            //Changing size
            if($width/$height > $ratio_orig){
                $width = $height*$ratio_orig;
            } else{
                $height = $height*$ratio_orig;
            }
            $image_p = imagecreatetruecolor($width, $height);
            imagecopyresampled($image_p, $img, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

            //Creating the backgroung
            $background = imagecreatetruecolor(500,500);
            imagecopy($background,$image_p,75,25,0,0,350,350);
            //Deleting 
            Deleting($name, $background, '/first.png');
            imagedestroy($img);
        }
        if(!empty($_POST['uppertext'])|| !empty($_POST['undertext'])){
            $font = __DIR__ . '/9605.ttf';
            $uppertext = $_POST['uppertext'];
            $uppertext = Text($uppertext, 30,$font);
            
            $undertext = $_POST['undertext'];
            $undertext = Text($undertext,10,$font);
            $demotivator = imagecreatefrompng('uploads/first.png');
            $color = imagecolorallocate($demotivator, 225,225,225);
            

            imagettftext($demotivator, 30, 0, 80, 410, $color, $font, $uppertext);
            imagettftext($demotivator, 10, 0, 150, 425, $color, $font, $undertext);
            echo '<img src="uploads/result.png" id="pic">';
            Deleting('uploads/first.png', $demotivator, '/result.png');
        } else{
            echo "Text is required!";
            unlink('uploads/first.png');
        }
    }
}
include 'index.html';
?>