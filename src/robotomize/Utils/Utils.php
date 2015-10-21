<?php
    /**
     * This file is part of the Fujes package.
     * @link    https://github.com/robotomize/fujes
     * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
     */
namespace robotomize\Utils;

/**
 * Class Utils - common utils
 * @package robotomize\Utils
 * @author robotomzie@gmail.com
 */
class Utils
{
    /**
     * Curl wrapper, check gziped connection
     *
     * @param string $url
     *
     * @return mixed
     */
    public function curlWrap($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_USERAGENT, 'cURL');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }
}
