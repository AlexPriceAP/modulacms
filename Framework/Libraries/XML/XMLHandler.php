<?php

class XmlHandler {

    function xml2array($fname) {
        $sxi = new SimpleXmlIterator($fname, null, true);
        return $this->sxiToArray($sxi);
    }

    function sxiToArray($sxi) {
        $a = array();
        for( $sxi->rewind(); $sxi->valid(); $sxi->next() ) {
            if(!array_key_exists($sxi->key(), $a)) {
                $a[$sxi->key()] = array();
            }
            if($sxi->hasChildren()) {
                $a[$sxi->key()][] = $this->sxiToArray($sxi->current());
            }
            else {
                $a[$sxi->key()][] = strval($sxi->current());
            }
        }
        return $a;
    }

}