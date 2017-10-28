<?php


namespace Digitalkreativ\Utilities;


class ValidationUtils
{

    public static function notEmptyString( $string )
    {
        if( !is_string( $string ) ){
            return false;
        } elseif ( trim( $string ) == ''){
            return false;
        }

        return true;
    }

    public static function isEmptyString( $string )
    {
        if( !is_string( $string ) ){
            return false;
        } elseif( trim( $string) === ''){
            return true;
        }

        return false;
    }

    public static function isStringAlphaNumericWithUnderScores( $string )
    {
        if( !is_string( $string ) ){
            return  false;
        } elseif( trim($string) === ''){
            return false;
        } else {
            $stringNoUnderScores = str_replace('_','',$string);
            return ctype_alnum( $stringNoUnderScores );
        }
    }

    public static function isJson( $json )
    {
        if( !is_null( json_decode( $json ) ) ){
            return true;
        }

        return false;
    }


    /*
     |-----------------------------------
     | ALIASES
     |-----------------------------------
     */

    public static function isEmail( $email )
    {
        return EmailUtils::isEmail( $email );
    }

    public static function isIpV4( $ipAddress )
    {
        return IpUtils::checkIpIsV4( $ipAddress );
    }

    public static function isIpV6( $ipAddress )
    {
        return IpUtils::checkIpIsV6( $ipAddress );
    }

    public static function isIpInRange( $ipAddress, $ipRange )
    {
        return IpUtils::checkIp( $ipAddress, $ipRange );
    }



}