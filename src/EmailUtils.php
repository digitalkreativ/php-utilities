<?php


namespace Digitalkreativ\Utilities;


class EmailUtils
{

    /**
     * @param $email
     * @param bool $returnEmailInsteadOfTrue
     *
     * @return bool|string
     */
    public static function isEmail( $email, $returnEmailInsteadOfTrue=false )
    {

        if( trim( $email ) == ""){
            return false;
        }

        //Default result for filter_var is not enough for the more exotic domain names
        $currentResult = filter_var( $email, FILTER_VALIDATE_EMAIL );

        if( $currentResult === false && substr_count( $email, '@') == 1 ){

            list($receiverPart,$domainPart) = explode('@', $email);

            $errorCode = null;

            // More exotic domain names need to be converted to their none exotic counterparts
            if( function_exists('idn_to_ascii') ){
                $idnToAscii = idn_to_ascii( $domainPart, $errorCode );

                if( $idnToAscii != $domainPart ){
                    //We have an actual puni coded email address
                    $email = $receiverPart . '@' . $idnToAscii;

                    $currentResult = filter_var( $email, FILTER_VALIDATE_EMAIL);

                    if( $currentResult === false ){
                        return false;
                    }
                }
            }

        }

        if( $currentResult != $email ){
            return false;
        }

        if( $returnEmailInsteadOfTrue == true ){
            return $currentResult;
        }

        return true;
    }

}