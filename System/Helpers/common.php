<?php

class Common {

    public static function getTimestamp($data, $format) {
		list($splitter) = str_replace(array('d', 'm', 'y'), '', strtolower($format));
	
		$data = explode($splitter, $data);
		$format = explode($splitter, $format);
	
		for ($c = 0; $c < count($format); $c++) {
		    switch ($format[$c]) {
			case 'd':
			    $day = $data[$c];
			    break;
			case 'm':
			    $month = $data[$c];
			    break;
			case 'y':
			case 'Y':
			    $year = $data[$c];
			    break;
		    }
		}
		return mktime(0, 0, 0, $month, $day, $year);
    }

    public static function normalizeString($string) {
        $search = explode(","," ,ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
        $replace = explode(",","_,c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
        return strtolower(str_replace($search, $replace, $string));
    }
}

?>
