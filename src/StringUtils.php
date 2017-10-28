<?php


namespace Digitalkreativ\Utilities;

use Illuminate\Support\Str;


class StringUtils
{
    public static function label( $fieldName )
    {
        $label = Str::lower( $fieldName );
        $label = str_replace( ['-','_','[]'], ' ', $label );
        $label = Str::ucfirst( $label );
        return $label;
    }

    public static function getAllPartsInStringSeparatedBySpecificSeparators( $string, $separators=[',',';'], $skipEmptyElements=true, $uniqueElements=true )
    {
        $foundSeparators = [];
        foreach( $separators as $separator ){
            if( strpos( $string, $separator) !== false ){
                $foundSeparators[] = $separator;
            }
        }

        $foundSeparators = array_unique( $foundSeparators );

        $possibleStrings = [];
        if( count( $foundSeparators ) == 0 ){
            $possibleStrings[] = $string;
        } elseif( count( $foundSeparators ) == 1 ){
            $separator = array_pop( $foundSeparators );
            $possibleStrings = explode( $separator, $string );


            if( $skipEmptyElements === true ){
                foreach( $possibleStrings as $index => $possibleString ){
                    if ( $possibleString == '') {
                        unset($possibleStrings[$index]);
                        reset($possibleStrings);
                    }
                }

                $possibleStrings = array_values( $possibleStrings );
            }

        } else {

            $stringLength = strlen( $string );
            $dummy = '';
            for( $i = 0; $i < $stringLength; ++$i ){
                $char = substr($string, $i, 1);
                if( !in_array( $char, $foundSeparators ) ){
                    $dummy.= $char;
                } else {

                    if( $skipEmptyElements === true && $dummy == ''){
                        $dummy = '';
                        continue;
                    }

                    $possibleStrings[] = $dummy;
                    $dummy = '';
                }
            }

            if( $skipEmptyElements === true && $dummy == ''){
                return $possibleStrings;
            }

            $possibleEmails[] = $dummy;
        }

        if( $uniqueElements === true ){
            $possibleStrings = array_unique( $possibleStrings );
        }

        return $possibleStrings;

    }

    public static function createArrayOutOfOnePerLine( $string )
    {
        $array = explode( PHP_EOL, $string );

        $newArray = array();
        foreach( $array as $element ){
            $newElement = trim( $element );
            if( $newElement != ''){
                $newArray[] = $newElement;
            }
        }

        return $newArray;
    }

    public static function createOnePerLineOutOfArray( $array )
    {
        if( !is_array( $array ) ){
            return $array;
        }

        return implode( PHP_EOL, $array);
    }


    public static function getUrlSafeString( $string )
    {
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    }

    public static function getHash( $length=10 )
    {
        return static::getRandomCharacters( $length, false, true, true );
    }

    /**
     * get a random character string of x length
     * @param $length
     * @return string
     */
    public static function getRandomCharacters($length, $upperCase=true, $lowerCase=true, $digits=true)
    {
        $token = "";

        $codeAlphabet = "";
        if($upperCase){
            $codeAlphabet.= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        if($lowerCase){
            $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        }
        if($digits){
            $codeAlphabet.= "0123456789";
        }

        $max = strlen($codeAlphabet) - 1;
        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[ self::_cryptoRandSecure(0, $max)];
        }
        return $token;
    }

    /**
     * @param $min
     * @param $max
     * @return mixed
     */
    private static function _cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }


}