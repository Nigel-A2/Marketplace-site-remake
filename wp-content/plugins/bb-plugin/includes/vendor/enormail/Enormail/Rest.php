<?php namespace Enormail;
/**
 * The base REST class for Enormail API
 *
 * This class provides all the tools to communicate
 * with a REST API.
 *
 * @package Enormail API
 * @version 1.0
 * @author Enormail
 */
class Rest {

    protected $host = 'https://api.enormail.eu/api/1.0/';

    protected $key = '';

    protected $version = '1.0';

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function get($uri, $params = array())
    {
        return $this->_exec('GET', $uri, $params);
    }

    public function post($uri, $params = array())
    {
        return $this->_exec('POST', $uri, $params);
    }

    public function put($uri, $params = array())
    {
        return $this->_exec('PUT', $uri, $params);
    }

    public function delete($uri, $params = array())
    {
        return $this->_exec('DELETE', $uri, $params);
    }

    private function _exec($method, $uri, $params = array())
    {
        // Init
        $ch  = curl_init();
        $uri = ltrim($uri, '/');

        // Options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->key.':password');
        curl_setopt($ch, CURLOPT_USERAGENT, 'EM REST API WRAPPER '.$this->version);
        $ch = fl_set_curl_safe_opts( $ch );

        // Set request
        switch(strtoupper($method))
        {
            case 'GET' :

                curl_setopt($ch, CURLOPT_URL, $this->host . $uri  . '?' . http_build_query($params));

            break;

            case 'POST' :

                curl_setopt($ch, CURLOPT_URL, $this->host . $uri);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_prep_post_vars($params));

            break;

            case 'PUT' :

                curl_setopt($ch, CURLOPT_URL, $this->host . $uri);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_prep_post_vars($params));

            break;

            case 'DELETE' :

                curl_setopt($ch, CURLOPT_URL, $this->host . $uri  . '?' . http_build_query($params));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

            break;
        }

        // Fetch output
        $output = curl_exec($ch);
        $code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close connection
        curl_close($ch);

        // Set response
        $return = new Response(array(
            'code' => $code,
            'response' => $output
        ));

        // Return
        return (string) $return;
    }

    private function _prep_post_vars($vars, $sep = '&')
    {
        $str = '';

        foreach ($vars as $k => $v)
        {
            if (is_array($v))
            {
                foreach($v as $vk => $vi)
                {
                    $str .= urlencode($k).'['.$vk.']'.'='.urlencode($vi).$sep;
                }
            }
            else
            {
                $str .= urlencode($k).'='.urlencode($v).$sep;
            }
        }

        return substr($str, 0, -1);
    }

}

class Response {

    public function __construct($response)
    {
        // Set response
        $this->http_code = $response['code'];
        $this->http_response = $response['response'];
    }

    public function __toString()
    {
        return $this->http_response;
    }


}
