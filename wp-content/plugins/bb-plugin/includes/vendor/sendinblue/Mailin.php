<?php

/**
 * Mailin REST client
 */
class Mailin_Rest
{
    public $api_key;
    public $base_url;
    public $curl_opts = array();
    public function __construct($base_url,$api_key)
    {
        if(!function_exists('curl_init'))
        {
            throw new Exception('Mailin requires CURL module');
        }
        $this->base_url = $base_url;
        $this->api_key = $api_key;
    }
    /**
    * Do CURL request with authorization
    */
    private function do_request($resource,$method,$input)
    {
        $called_url = $this->base_url."/".$resource;
        $ch = curl_init($called_url);
        $auth_header = 'api-key:'.$this->api_key;
        $content_header = "Content-Type:application/json";
        $ch = fl_set_curl_safe_opts( $ch );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($auth_header,$content_header));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
        $data = curl_exec($ch);
        if(curl_errno($ch))
        {
            echo 'Curl error: ' . curl_error($ch). '\n';
        }
        curl_close($ch);
        return json_decode($data,true);
    }
    public function get($resource,$input)
    {
        return $this->do_request($resource,"GET",$input);
    }
    public function put($resource,$input)
    {
        return $this->do_request($resource,"PUT",$input);
    }
    public function post($resource,$input)
    {
        return $this->do_request($resource,"POST",$input);
    }
    public function delete($resource,$input)
    {
        return $this->do_request($resource,"DELETE",$input);
    }
    public function get_account()
    {
        return $this->get("account","");
    }
    public function get_lists($page_limit)
    {
        return $this->get("contacts/lists",json_encode(array("limit"=>$page_limit, "offset"=>0)));
    }
    public function create_update_user($email,$attributes,$blacklisted,$listid,$update_enabled,$listid_unlink,$blacklisted_sms)
    {
        return $this->post("contacts",json_encode(array("email"=>$email,"attributes"=>$attributes,"emailBlacklisted"=>$blacklisted,"listIds"=>$listid,"updateEnabled"=>$update_enabled,"smsBlacklisted"=>$blacklisted_sms)));
    }
    public function get_contact_attributes()
    {
        $attrs = $this->get("contacts/attributes","");
        $attr_names = array();
        if ( ! empty( $attrs['attributes'] ) ) {
                foreach ( $attrs['attributes'] as $attr ) {
                        $attr_names[] = $attr['name'];
                }
        }
        return $attr_names;
    }
}
?>
