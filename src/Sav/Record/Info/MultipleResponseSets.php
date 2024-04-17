<?php

namespace SPSS\Sav\Record\Info;

use SPSS\Buffer;
use SPSS\Sav\Record\Info;


class MultipleResponseSets
    extends Info
{

    const SUBTYPE   = 7;
    const DELIMITER = "\n";

    public $sets = [];

    public function read( Buffer $buffer )
    {

        parent::read( $buffer );
        $data = rtrim( $buffer->readString( $this->dataSize * $this->dataCount ) );

        $this->data['raw'] = $data;

        foreach ( explode( self::DELIMITER, $data ) as $item ) {
            list($key, $value) = explode( '=', $item );
            $method = substr($value,0,1);
            if ($method =='C'){
                list($m, $l) = explode( ' ', $value );
                $v='';
                $trim = 2 + strlen($m) + strlen($l);
            }else{
                list($m, $v, $l) = explode( ' ', $value );
                $trim = 3 + strlen($m) + strlen($l) + strlen($v);
            }

            $this->sets[$key]['set'] = substr( $key,1 );
            $this->sets[$key]['method'] = trim( $method );
            $this->sets[$key]['label'] = substr($value,$trim,$l);
            $this->sets[$key]['variables'] = explode(' ', substr($value,$trim+$l+1));


        }


    }

    public function write( Buffer $buffer )
    {
        if ( !isset( $this->data['raw'] ) ) {
            $this->data['raw'] = '';
        }
        $this->dataCount = \strlen( $this->data['raw'] );
        parent::write( $buffer );
        $buffer->writeString( $this->data['raw'] );
    }

}
