<?php
namespace Keexybox;


if(!defined('IP4CALC_CLASS')) {
define('IP4CALC_CLASS', true);


/**
 * This class is about converting IPv4 addresses between the different format available and
 * calculating informations out of the IPv4 and netmask provided.
 * @package IP4Calc
 * @author Florian MAURY <pub[DASH]ip4calc[AT]x[DASH]cli[DOT]com>
 * @version 1.1, 08/18/09
 * @history 08/18/09 Integrating the constants in the class, adding checks for the get function
*/
class IP4Calc {
    // Definition of constants to abstract the usage from the internal namespace
    const IP='IP';
    const NETMASK='Netmask';
    const NETWORK='Network';
    const BROADCAST='Broadcast';
    const MIN_HOST='MinHost';
    const MAX_HOST='MaxHost';
    const PREVIOUS_HOST='PrevHost';
    const NEXT_HOST='NextHost';
    const PREVIOUS_NETWORK='PrevNetwork';
    const NEXT_NETWORK='NextNetwork';
    const INT32='Int32';
    const BIN='Bin';
    const HEX='Hex';
    const QUAD_DOTTED='Quad';
    const DECIMAL='Dec';

    /**
     * Contains data provided, calculated and cached about the IP address used in this instance
     * @access private
     * @var Array
    */
    private $aAddresses;

    /**
     * @access public
     * @param string $sIP IP address in binary, hexadecimal or dotted quad format
     * @param mixed $mNetmask The netmask of the IP address in binary, hexadecimal, dotted quad or decimal (e.g. 24 for xxx.xxx.xxx.xxx/24)
     * @throws Exception If the IP address or the netmask are not provided in a valid format
    */
    public function __construct($sIP, $mNetmask) {
        $this->aAddresses = array();
        $this->aAddresses[self::IP] = array();
        $this->aAddresses[self::NETMASK] = array();
        $this->aAddresses[self::NETWORK] = array();
        $this->aAddresses[self::BROADCAST] = array();
        $this->aAddresses[self::MIN_HOST] = array();
        $this->aAddresses[self::MAX_HOST] = array();
        $this->aAddresses[self::PREVIOUS_HOST] = array();
        $this->aAddresses[self::NEXT_HOST] = array();
        $this->aAddresses[self::PREVIOUS_NETWORK] = array();
        $this->aAddresses[self::NEXT_NETWORK] = array();

        list($sFormat, $iIP) = $this->convertIPToInt32($sIP);
        $this->aAddresses[self::IP][$sFormat] = $sIP;
        $this->aAddresses[self::IP][self::INT32] = $iIP;

        // Detecting deciaml format for netmask
        if(preg_match('/^[0-9]{1,2}$/', (string) $mNetmask) > 0 && (integer) $mNetmask <= 32) {
            $this->aAddresses[self::NETMASK][self::DECIMAL] = (integer) $mNetmask;
            $this->aAddresses[self::NETMASK][self::INT32] = self::convertDecToInt32((integer) $mNetmask);
        }
        // Detecting Quad dotted format for netmask
        elseif(preg_match('/^((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))\.((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))\.((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))\.((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))$/', (string)$mNetmask, $aQuad) > 0) {
            $this->aAddresses[self::NETMASK][self::QUAD_DOTTED] = $mNetmask;
            array_shift($aQuad); // Removing the first field containing the whole string
            $this->aAddresses[self::NETMASK][self::INT32] = self::convertQuadToInt32($aQuad);
        }
        // Detecting hexadecimal format for netmask
        elseif(preg_match('/^(?:Ox)?(?:([0-9a-f])[:.\-]?$)/i', (string)$mNetmask, $aHex) > 0) {
            $this->aAddresses[self::NETMASK][self::HEX] = $mNetmask;
            $this->aAddresses[self::NETMASK][self::INT32] = self::convertHexToInt32($aHex);
        }
        // Detecting binary format for netmask
        elseif(preg_match('/^[0-1]{32}$/', (string)$mNetmask) > 0) {
            $this->aAddresses[self::NETMASK][self::BIN] = $mNetmask;
            $this->aAddresses[self::NETMASK][self::INT32] = self::convertBinToInt32($mNetmask);
        }
        else {
            throw new Exception('Unknown format for this netmask parameter.');
        }
    }
    
    /**
     * Convert an IP address from the dotted quad format to an unsigned int32.
     * @access public
     * @static
     * @param mixed $mQuad The IP address to convert. Can be a string containing the dotted quad address or a 4 slots array.
     * @throws Exception the parameter is not a valid dotted quad IP address
     * @return uint32 Since PHP doesn't handle unsigned int, it's a float.
    */
    static public function convertQuadToInt32($mQuad) {
        if(is_string($mQuad) === true) {
            // Checking the ip format + removing the eventual netmask
            if(preg_match('/^((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))\.((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))\.((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))\.((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))$/i', $mQuad, $aQuad) > 0) {
                array_shift($aQuad); // Removing the first field containing the whole string
            }
            else {
                throw new Exception('Bad format. Expect a quad dotted address.');
            }
        }
        else {
            $aQuad = $mQuad;
        }
        $iInt32Format = 0;
        for($i = 0 ; $i < 4 ; $i++) {
            $iInt32Format += (integer)$aQuad[3 - $i] * pow(2,8*$i);
        }
        return $iInt32Format;
    }

    /**
     * Convert an IP address from an unsigned int32 to a dotted quad.
     * @access public
     * @static
     * @param uint32 $iQuad The IP address to convert. Since PHP does'nt handle unsigned int, this is a float.
     * @return string A dotted quad address
    */
    static public function convertInt32ToQuad($iQuad) {
        $mask = pow(2, 8) - 1;
        return (string)(($iQuad / pow(2,24)) & $mask).'.'.(string)(($iQuad / pow(2,16)) & $mask).'.'.(string)(($iQuad / pow(2,8)) & $mask).'.'.(string)($iQuad & $mask);
    }

    /**
     * Convert an IP address from a hexadecimal expression to an unsigned int32.
     * @access public
     * @static
     * @param mixed $mHex The IP address to convert in hexadecimal. Can be a string representing the hexadecimal number or a 4 slots array.
     * @return uint32 Since PHP doesn't handle unsigned int, it's a float.
    */
    static public function convertHexToInt32($mHex) {
        $sHex='';
        if(is_array($mHex) === true) {
            $sHex = implode('', $mHex);
        }
        return hexdec($sHex);
    }

    /**
     * Convert an IP address from an unsigned int32 to a hexadecimal expression.
     * @access public
     * @static
     * @param uint32 $iHex The IP address to convert. Since PHP doesn't handle unsigned int, it's a float.
     * @return string The IP address in the hexadecimal format
    */
    static public function convertInt32ToHex($iHex) {
        return '0x'.dechex($iHex);
    }

    /**
     * Convert an IP address from binary format to an unsigned int32.
     * @access public
     * @static
     * @param string $sBin The IP address to convert. It's a 32 Bytes string of 1 and 0.
     * @return uint32 Since PHP doesn't handle unsigned int, it's a float.
    */
    static public function convertBinToInt32($sBin) {
        return bindec($sBin);
    }

    /**
     * Convert an IP address from an unsigned int32 to a string of 1 and 0 (binary format).
     * @access public
     * @static
     * @param uint32 $iBin The IP address to convert. Since PHP doesn't handle unsigned int, it's a float.
     * @return string It's a string of 1 and 0 representing the ip address in binary
    */
    static public function convertInt32ToBin($iBin) {
        return decbin($iBin);
    }

    /**
     * Convert a netmask from the short decimal format to an unsigned int32
     * @access public
     * @static
     * @param integer $iNetmask A short decimal netmask (e.g. 27 for xxx.xxx.xxx.xxx/27)
     * @return uint32 The netmask in uint32 format. Since PHP doesn't handle unsigned int32, it's a float
    */
    static public function convertDecToInt32($iNetmask) {
        $iInt32Format = 0;
        for($i = 0 ; $i < $iNetmask ; $i++) {
            $iInt32Format += pow(2,31-$i);
        }
        return $iInt32Format;
    }

    /**
     * Convert a netmask from an unsigned int32 to a short decimal
     * @access public
     * @static
     * @param uint32 $iNetmask The netmask in uint32 format. Since PHP doesn't handle unsigned int32, it's a float
     * @return integer A short decimal netmask (e.g. 27 for xxx.xxx.xxx.xxx/27)
    */
    static public function convertInt32ToDec($iNetmask) {
        $iDecFormat = 0;
        $bOver = false;
        for($i = 0 ; $i < 32 && $bOver === false ; $i++) {
            if((($iNetmask / pow(2,31-$i)) & 1) !== 0) {
                $iDecFormat++; 
            }
            else {
                $bOver = true;
            }
        }
        return $iDecFormat;
    }

    /**
     * Get the network address of the given IP/Netmask couple
     * @access public
     * @static
     * @param uint32 $iIP An IP address from the subnet for which you want the network address. Since PHP doesn't handle unsigned int32, it's a float 
     * @param uint32 $iNetmask The netmask of the subnet for which you want the network address. Since PHP doesn't handle unsigned int32, it's a float 
     * @return uint32 The network IP address in uint32 format. Since PHP doesn't handle unsigned int32, it's a float
    */
    static public function getNetwork($iIP, $iNetmask) {
    // Little trick to solve the problem of the unability of PHP to do bitwise operation on unsigned int32.
    // I don't want to force people to use the gmp module
        $mask=pow(2,16) - 1;
        return (((($iIP / pow(2,16)) & $mask) & (($iNetmask / pow(2,16)) & $mask)) * pow(2,16)) + (($iIP & $mask) & ($iNetmask & $mask));
    }

    /**
     * Get the broadcast address of the given IP/Netmask couple
     * @access public
     * @static
     * @param uint32 $iIP An IP address from the subnet for which you want the broadcast address. Since PHP doesn't handle unsigned int32, it's a float 
     * @param uint32 $iNetmask The netmask of the subnet for which you want the broadcast address. Since PHP doesn't handle unsigned int32, it's a float 
     * @return uint32 The broadcast IP address in uint32 format. Since PHP doesn't handle unsigned int32, it's a float
    */
    static public function getBroadcast($iIP, $iNetmask) {
        return self::getNetwork($iIP,$iNetmask) + ((pow(2,32) - 1) - $iNetmask); 
    }

    /**
     * Get the IP addresses of the different element that can be generated from the IP address given in the constructor.
     * @access public
     * @param string $sTarget The name of the element you want to get. Please, use the constants defined at the beginning of this file
     * @param string $sType The name of the return type of the element you want to get. Please, use the constants defined at the beginning of this file
     * @throws Exception If the $sTarget parameter is not a valid target
     * @return mixed The element requested in the type requested. Can be null if the element requested doesn't exist (case of previous host, previous network, next host, next network)
    */
    public function get($sTarget, $sType) {
        switch($sTarget) {
            case self::IP:
            case self::NETMASK:
            case self::NETWORK:
            case self::BROADCAST:
            case self::MIN_HOST:
            case self::MAX_HOST:
            case self::PREVIOUS_HOST:
            case self::NEXT_HOST:
            case self::PREVIOUS_NETWORK:
            case self::NEXT_NETWORK:
                break;
            default:
                throw new Exception('Unknown target.');
                break;
        }
        switch($sType) {
            case self::INT32:
            case self::QUAD_DOTTED:
            case self::HEX:
            case self::BIN:
                break;
            case self::DECIMAL:
                if($sTarget !== self::NETMASK) {
                    throw new Exception('Invalid format for this target');
                }
        break;
            default:
                throw new Exception('Invalid format');
                break;
        }

        if(false === isset($this->aAddresses[$sTarget][$sType])) {
            if(false === isset($this->aAddresses[$sTarget][self::INT32])) {
                $this->compute($sTarget);
            }
            if($sType !== self::INT32) {
                $sFunctionName = 'convertInt32To'.$sType;
                $this->aAddresses[$sTarget][$sType] = self::$sFunctionName($this->aAddresses[$sTarget][self::INT32]);
            }
        }
        return $this->aAddresses[$sTarget][$sType];
    }

    /**
     * Get the number of IP addresses in the subnet of the IP provided in the constructor
     * @access public
     * @return integer The number of IP addresses in the subnet
    */
    public function count() {
        return (pow(2,32) - 1) - $this->get(self::NETMASK, self::INT32) - 1;
    }

    /**
     * Test if an IP address is part of the subnet of the IP address provided in the constructor
     * @access public
     * @param string $sIP The IP address to test
     * @return boolean True if the IP address provided is in the subnet, otherwise false
    */
    public function partOf($sIP) {
        list($sFormat, $iIP) = $this->convertIPToInt32($sIP);        
        return ($this->get(self::MIN_HOST, self::INT32) <= $iIP && $this->get(self::MAX_HOST, self::INT32) >= $iIP);
    }

    /**
     * Compute the int32 format of the element requested in parameter and store it in the addresses table property of the class
     * @access private
     * @param string $sTarget The name of the element to be computed
     * @throws Exception If the $sTarget parameter is not a valid target
    */
    private function compute($sTarget) {
        switch($sTarget) {
            case self::NETWORK:
                $this->aAddresses[$sTarget][self::INT32] = self::getNetwork($this->get(self::IP, self::INT32), $this->get(self::NETMASK, self::INT32));
            break;
            case self::BROADCAST:
                $this->aAddresses[$sTarget][self::INT32] = self::getBroadcast($this->get(self::IP, self::INT32), $this->get(self::NETMASK, self::INT32));
            break;
            case self::MIN_HOST:
                $this->aAddresses[self::MIN_HOST][self::INT32] = $this->get(self::NETWORK, self::INT32) + 1;
            break;
            case self::MAX_HOST:
                $this->aAddresses[self::MAX_HOST][self::INT32] = $this->get(self::BROADCAST, self::INT32) - 1;
            break;
            case self::PREVIOUS_HOST:
                $this->aAddresses[self::PREVIOUS_HOST][self::INT32] = (($this->get(self::IP, self::INT32) - 1) === $this->get(self::NETWORK, self::INT32))?null:$this->get(self::IP, self::INT32) - 1; 
            break;
            case self::NEXT_HOST:
                $this->aAddresses[self::NEXT_HOST][self::INT32] = (($this->get(self::IP, self::INT32) + 1) === $this->get(self::BROADCAST, self::INT32))?null:$this->get(self::IP, self::INT32) + 1; 
            break;
            case self::PREVIOUS_NETWORK:
                $this->aAddresses[self::PREVIOUS_NETWORK][self::INT32] = ($this->get(self::NETWORK, self::INT32) === 0)?null:self::getNetwork($this->get(self::NETWORK, self::INT32) - 1, $this->get(self::NETMASK, self::INT32)); 
            break;
            case self::NEXT_NETWORK:
                $this->aAddresses[self::NEXT_NETWORK][self::INT32] = ($this->get(self::BROADCAST, self::INT32) === (255*pow(2,24) + 255*pow(2,16) + 255 * pow(2,8) + 255))?null:$this->get(self::BROADCAST, self::INT32) + 1;
            break;
            default:
                throw new Exception('Unknown compute target.');
        }
    }

    /**
     * Convert an IP address in any format into a uint32
     * @access private
     * @param string $sIP The IP address to convert
     * @throws Exception If the $sIP parameter is not a valid IP address
     * @return array(string, uint32) The format detected and the IP address in uint32 format. Since PHP doesn't handle unsigned int32, it's a float.
    */
    private function convertIPToInt32($sIP) {
        // Detecting Quad Dotted format for ip
        if(preg_match('/^((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))\.((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))\.((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))\.((?:1?[0-9]{1,2})|(?:2[0-4][0-9])|(?:25[0-5]))$/', $sIP, $aQuad) > 0) {
            array_shift($aQuad); // Removing the first field containing the whole string
            $sFormat = self::QUAD_DOTTED;
            $iIP = self::convertQuadToInt32($aQuad);
        }
        // Detecting hexadecimal format for ip
        elseif(preg_match('/^(?:Ox)?(?:([0-9a-f])[:.\-]?$)/i', $sIP, $aHex) > 0) {
            array_shift($aHex); // Removing the first field containing the whole string
            $sFormat = self::HEX;
            $iIP = self::convertHexToInt32($aHex);
        }
        // Detecting binary format for ip
        elseif(preg_match('/^[0-1]{32}$/', $sIP) > 0) {
            $sFormat = self::BIN;
            $iIP = self::convertBinToInt32($sIp);
        }
        // IP is not provided in a valid format
        else{
            throw new Exception('Unknown format for the IP parameter.');
        }
        return array($sFormat, $iIP);
    }
}

}
?>
