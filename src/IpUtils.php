<?php


namespace Digitalkreativ\Utilities;


class IpUtils
{
    private static $checkedIps = [];


    public static function isLoopbackIp( $ipAddress )
    {
        if( static::checkIpIsV4( $ipAddress ) ){
            return static::checkIpV4IsInRange( $ipAddress, '127.0.0.0/8' );
        } elseif( static::checkIpIsV6( $ipAddress ) ){
            return static::checkIpV6IsInRange( $ipAddress, '::1/128');
        }
    }

    public static function isUnspecifiedIp( $ipAddress )
    {
        if( static::checkIpIsV4( $ipAddress ) ){
            return static::checkIpV4IsInRange( $ipAddress, '0.0.0.0/0');
        } elseif( static::checkIpIsV6( $ipAddress ) ){
            return static::checkIpV6IsInRange( $ipAddress, '::/0');
        }
    }

    public static function isRfc1918InternalIp( $ipAddress )
    {
        //Check if proxy
        $internalIpRanges = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16'
        ];

        $isInternalIp = false;

        foreach( $internalIpRanges as $internalIpRange ){
            if( static::checkIpV4IsInRange( $ipAddress, $internalIpRange ) ){
                $isInternalIp = true;
                break;
            }
        }

        return $isInternalIp;
    }

    public static function checkIpIsV4( $ipAddress )
    {
        if( filter_var( $ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) === false ){
            return false;
        }
        return true;
    }

    public static function checkIpIsV6( $ipAddress )
    {
        if( filter_var( $ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) === false ){
            return false;
        }
        return true;
    }

    /**
     * Check if a given ip is in a network
     *
     * @param  string $ipAddress    IP to check in IPV4 format eg. 127.0.0.1
     * @param  string $ipRange IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
     * @return boolean true if the ip is in this range / false if not.
     */
    public static function checkIpV4IsInRange( $ipAddress, $ipRange )
    {
        if( static::checkIpIsV4( $ipAddress ) === false ){
            return false;
        }

        try {

            return static::checkIp4( $ipAddress, $ipRange);

        } catch ( \Exception $ex ){
            Log::debug( __CLASS__ . ' ' . __FUNCTION__ . ' - ' . $ex->getMessage() );

            if ( strpos( $ipRange, '/' ) == false ) {
                $ipRange .= '/32';
            }

            // $range is in IP/CIDR format eg 127.0.0.1/24
            list( $ipRange, $netMask ) = explode( '/', $ipRange, 2 );

            $rangeDecimal = ip2long( $ipRange );
            $ipDecimal = ip2long( $ipAddress );
            $wildcardDecimal = pow( 2, ( 32 - $netMask ) ) - 1;
            $netmaskDecimal = ~ $wildcardDecimal;

            return ( ( $ipDecimal & $netmaskDecimal ) == ( $rangeDecimal & $netmaskDecimal ) );

        }

    }

    public static function checkIpV6IsInRange( $ipAddress, $ipRange )
    {
        if( static::checkIpIsV6( $ipAddress ) === false ){
            return false;
        }

        try {

            return static::checkIp6( $ipAddress, $ipRange);

        } catch ( \Exception $ex ){
            Log::debug( __CLASS__ . ' ' . __FUNCTION__ . ' - ' . $ex->getMessage() );
            return false;
        }

    }


    /**
     * Checks if an IPv4 or IPv6 address is contained in the list of given IPs or subnets.
     *
     * @param string       $requestIp IP to check
     * @param string|array $ips       List of IPs or subnets (can be a string if only a single one)
     *
     * @return bool Whether the IP is valid
     */
    public static function checkIp($requestIp, $ips)
    {
        if (!is_array($ips)) {
            $ips = array($ips);
        }

        $method = substr_count($requestIp, ':') > 1 ? 'checkIp6' : 'checkIp4';

        foreach ($ips as $ip) {
            if (self::$method($requestIp, $ip)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compares two IPv4 addresses.
     * In case a subnet is given, it checks if it contains the request IP.
     *
     * @author Fabien Potencier <fabien at symfony dot com>
     *
     * @param string $requestIp IPv4 address to check
     * @param string $ip        IPv4 address or subnet in CIDR notation
     *
     * @return bool Whether the request IP matches the IP, or whether the request IP is within the CIDR subnet
     */
    public static function checkIp4($requestIp, $ip)
    {
        $cacheKey = $requestIp.'-'.$ip;
        if (isset(self::$checkedIps[$cacheKey])) {
            return self::$checkedIps[$cacheKey];
        }

        if (!filter_var($requestIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return self::$checkedIps[$cacheKey] = false;
        }

        if (false !== strpos($ip, '/')) {
            list($address, $netmask) = explode('/', $ip, 2);

            if ('0' === $netmask) {
                return self::$checkedIps[$cacheKey] = filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
            }

            if ($netmask < 0 || $netmask > 32) {
                return self::$checkedIps[$cacheKey] = false;
            }
        } else {
            $address = $ip;
            $netmask = 32;
        }

        if (false === ip2long($address)) {
            return self::$checkedIps[$cacheKey] = false;
        }

        return self::$checkedIps[$cacheKey] = 0 === substr_compare(sprintf('%032b', ip2long($requestIp)), sprintf('%032b', ip2long($address)), 0, $netmask);
    }

    /**
     * Compares two IPv6 addresses.
     * In case a subnet is given, it checks if it contains the request IP.
     *
     * @author David Soria Parra <dsp at php dot net>
     *
     * @see https://github.com/dsp/v6tools
     *
     * @param string $requestIp IPv6 address to check
     * @param string $ip        IPv6 address or subnet in CIDR notation
     *
     * @return bool Whether the IP is valid
     *
     * @throws \RuntimeException When IPV6 support is not enabled
     */
    public static function checkIp6($requestIp, $ip)
    {
        $cacheKey = $requestIp.'-'.$ip;
        if (isset(self::$checkedIps[$cacheKey])) {
            return self::$checkedIps[$cacheKey];
        }

        if (!((extension_loaded('sockets') && defined('AF_INET6')) || @inet_pton('::1'))) {
            throw new \RuntimeException('Unable to check Ipv6. Check that PHP was not compiled with option "disable-ipv6".');
        }

        if (false !== strpos($ip, '/')) {
            list($address, $netmask) = explode('/', $ip, 2);

            if ($netmask < 1 || $netmask > 128) {
                return self::$checkedIps[$cacheKey] = false;
            }
        } else {
            $address = $ip;
            $netmask = 128;
        }

        $bytesAddr = unpack('n*', @inet_pton($address));
        $bytesTest = unpack('n*', @inet_pton($requestIp));

        if (!$bytesAddr || !$bytesTest) {
            return self::$checkedIps[$cacheKey] = false;
        }

        for ($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; ++$i) {
            $left = $netmask - 16 * ($i - 1);
            $left = ($left <= 16) ? $left : 16;
            $mask = ~(0xffff >> $left) & 0xffff;
            if (($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask)) {
                return self::$checkedIps[$cacheKey] = false;
            }
        }

        return self::$checkedIps[$cacheKey] = true;
    }
}