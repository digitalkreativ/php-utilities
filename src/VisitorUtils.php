<?php

namespace Digitalkreativ\Utilities;


use Illuminate\Support\Str;

class VisitorUtils
{

    /**
     * Get the IP address of the current request
     * @return string
     */
    public static function ipAddress( $headersToLookFor=['http_x_forwarded_for', 'remote_addr'] )
    {

        if( count( $headersToLookFor ) == 0 ){
            // Check for X-Forwarded-For headers and use those if found
            if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && ( '' !== trim( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) {
                return trim( $_SERVER['HTTP_X_FORWARDED_FOR'] );
            } elseif ( isset( $_SERVER['REMOTE_ADDR'] ) && ( '' !== trim( $_SERVER['REMOTE_ADDR'] ) ) ) {
                return trim( $_SERVER['REMOTE_ADDR'] );
            }
        } else {

            foreach( $headersToLookFor as $header ){

                if( isset( $_SERVER[ Str::upper( $header ) ] ) && ( '' !== trim( $_SERVER[ Str::upper( $header ) ] ) ) ){
                    return trim( $_SERVER[ Str::upper( $header ) ] );
                }

            }

        }

        return null;

    }

    /**
     * Get the hostname associated with the given ip address
     * returns the hostname on success, the ip if failed or bool false if malformed ip address
     *
     * @param $ipAddress
     * @return string|bool
     */
    public static function hostname( $ipAddress )
    {
        $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

        return $hostname;
    }

    public static function userAgent()
    {
        $userAgentString = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? $_SERVER['HTTP_USER_AGENT'] : '';

        return $userAgentString;
    }

}