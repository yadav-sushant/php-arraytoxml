<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
   
class Xml {
    function __construct(){
    
    }

    function arrayToXml($data, $object){
        foreach ($data as $key => $value) {
            $key_arr = explode('_',$key);
            $keyname = (count($key_arr)>1 && is_numeric($key_arr[1]))?$key_arr[0]:$key;
            $attr = $value["attr"]??[];
            $value = $value["val"]??[];
            
            if (is_array($value)) {
                $new_object = $object->addChild($keyname);
                if(!empty($attr)) foreach($attr as $k => $v) $new_object->addAttribute($k, $v);
                $this->arrayToXml($value, $new_object);
            } 
            else{ 
                $object->addChild($keyname, $value);
                if(!empty($attr)) foreach($attr as $k => $v) $object->addAttribute($k, $v);
            }
        }
    }

    function export($data = []){
        if(!empty($data)){
            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><root></root>');
            $this->arrayToXml($data,$xml);
            foreach ($xml->xpath('/root/child::'.key($data).'[1]') as $node) {
                $unwrappedXml = new SimpleXMLElement($node->asXML());
                break;
            }
            
            $str = $unwrappedXml->asXML();
            header('Content-Disposition: attachment; filename="tally'.time().'.xml"');
            header('Content-type: application/xml');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($str));
            header('Connection: close');
            echo $str;   
        }
    }
}
