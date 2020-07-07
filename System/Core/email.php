<?php
/**
 * 
 * Class E-Mail sender
 * @version 0.1a
 * @autor Menza Fabrizio
 *
 */

class email{

    public static function sendMail($mail_type, $language, $data = array())
    {
		return false; // blocca tutto
		
		
            if(!isset($data['mail_to']))
                return false;

            if($language == '')
            	$language = 'it';
            	
            if(file_exists('../languages/'.$language.'/mail/'.$mail_type.'.php')){
                include '../languages/'.$language.'/mail/'.$mail_type.'.php';                   
            }else{
            	trigger_error('E-Mail model not found: ../languages/'.$language.'/mail/'.$mail_type.'.php', E_USER_ERROR);
            }
             
            $subject = isset($subject) ? $subject : '';
                       
            foreach($data as $key=>$value){
                $html_text = str_replace( ":(".$key.')' , $value , $html_text );
                $txt_text = str_replace( ":(".$key.')' , $value , $txt_text );
            }
                
            /*
            //invio mail
            $header = "To: ".$data['first_name']." <".$data['mail_to'].">\n";
            $header .= "From: Meet and Goo <info@meetandgoo.com>\n";            
            */
            
            // costruiamo alcune intestazioni generali
            $header = "From: Meet and Goo <info@meetandgoo.com>\n";
            //$header .= "To: ".$data['first_name']." <".$data['mail_to'].">\n";

            // generiamo la stringa che funge da separatore
            $boundary = "==String_Boundary_x" .md5(time()). "x";

            // costruiamo le intestazioni che specificano
            // un messaggio costituito da più parti alternative
            $header .= "MIME-Version: 1.0\n";
            $header .= "Content-Type: multipart/alternative;\n";
            $header .= " boundary=\"$boundary\";\n\n";

            // questa parte del messaggio viene visualizzata
            // solo se il programma non sa interpretare
            // i MIME poiché è posta prima della stringa boundary
            $messaggio = "Se visualizzi questo testo il tuo programma non supporta i MIME\n\n";

            // inizia la prima parte del messaggio in testo puro
            $messaggio .= "--$boundary\n";
            $messaggio .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
            $messaggio .= "Content-Transfer-Encoding: 7bit\n\n";
            $messaggio .= $txt_text."\n\n";

            // inizia la seconda parte del messaggio in formato html
            $messaggio .= "--$boundary\n";
            $messaggio .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
            $messaggio .= "Content-Transfer-Encoding: 7bit\n\n";
            $messaggio .= $html_text."\n";

            // chiusura del messaggio con la stringa boundary
            $messaggio .= "--$boundary--\n";
            
            if(mail($data['mail_to'],$subject,$messaggio,$header))
            {
                return true;
            }
            else
            {
                trigger_error("Impossibile inviare l'e-mail");
                return false;                
            }
    }
    
    
    //-- Carica i file della lingua
    public static function loadLanguage($mail_type, $language)
    {              
        if(file_exists('languages/'.$language.'/mail/'.$mail_type.'.xml')){
            $xml = simplexml_load_file('languages/'.$language.'/mail/'.$mail_type.'.xml');                              
            return $xml->item;
        }
    }

}