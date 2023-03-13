<?php

/**
 * Mautic API Class
 */
class MauticApi
{
	/**
	 * API base URL where Mautic is installed
	 *
	 * @var string
	 */
    protected $baseUrl;

	/**
     * Password associated with Username
     *
     * @var string
     */
    private $password;

    /**
     * Username or email, basically the Login Identifier
     *
     * @var string
     */
    private $userName;

	/**
     * Holds string of HTTP response headers
     *
     * @var string
     */
    protected $_httpResponseHeaders;

    /**
     * Holds array of HTTP response CURL info
     *
     * @var array
     */
    protected $_httpResponseInfo;

	/**
	 * Log processes
	 *
	 * @param array $config [description]
	 */
	public $logs = array();

	/**
	 * Construct
	 *
	 * @param array $config
	 */
    public function __construct(array $config)
    {
        //error checking
        $apiUrl = @$config['apiUrl'];
        $api_username = @$config['userName'];
		$api_password = @$config['password'];

        if (empty($apiUrl)) {
            throw new Exception("Required config parameter [installation_url] is not set or empty", 1);
        }

		if (empty($api_username)) {
            throw new Exception("Required config parameter [api_username] is not set or empty", 1);
        }

        if (empty($api_password)) {
            throw new Exception("Required config parameter [api_password] is not set or empty", 1);
        }

        $this->userName = $api_username;
        $this->password = $api_password;

		$this->setBaseUrl($apiUrl);
    }

	protected function isAuthorized()
    {
        return (!empty($this->userName) && !empty($this->password));
    }

	/**
     * Subscribe a contact
     *
     * @param array $parameters
     *
     * @return array|mixed
     */
    public function subscribe(array $parameters)
    {
		$response = $this->createContact( $parameters );

		// Add contact to a list/segment
		if ( isset( $response[ 'contact' ][ 'id' ]) && isset( $parameters['segmentId'] ) ) {
			$this->addContactSegment( $parameters['segmentId'], $response[ 'contact' ][ 'id' ] );
		}

		return $response;
	}

	/**
     * Create a new contact
     *
     * @param array $parameters
     *
     * @return array|mixed
     */
    public function createContact(array $parameters)
    {
	 	return $this->makeRequest( 'contacts/new', $parameters, 'POST' );
    }

	/**
     * Add a contact to the campaign
     *
     * @param int $id        Campaign ID
     * @param int $contactId Contact ID
     *
     * @return array|mixed
     */
    public function addContactCampaign($id, $contactId)
    {
        return $this->makeRequest( 'campaigns/'.$id.'/contact/'.$contactId.'/add', array(), 'POST' );
    }

	/**
     * Add a contact to the segment
     *
     * @param int $segmentId Segment ID
     * @param int $contactId Contact ID
     *
     * @return array|mixed
     */
    public function addContactSegment($segmentId, $contactId)
    {
        return $this->makeRequest( 'segments/'.$segmentId.'/contact/'.$contactId.'/add', array(), 'POST' );
    }

	/**
     * Get campaigns list
     *
     * @param array $params
     * @return array|mixed
     */
	public function getCampaigns( array $params )
	{
		return $this->getList( 'campaigns', $params );
	}

	/**
     * Get segments list
     *
     * @param array $params
     * @return array|mixed
     */
	public function getSegments( array $params = array() )
	{
		return $this->getList( 'segments', $params );
	}

	/**
     * Get a list of items
     *
     * $params:
     * @param string $context
     * @param string $search
     * @param int    $start
     * @param int    $limit
     * @param string $orderBy
     * @param string $orderByDir
     * @param bool   $publishedOnly
     * @param bool   $minimal
     *
     * @return array|mixed
     */
    public function getList($context, array $params)
    {
        $parameters = array(
            'search'        => '',
            'start'         => 0,
            'limit'         => 0,
            'orderBy'       => '',
            'orderByDir'    => 'ASC',
            'publishedOnly' => false,
            'minimal'       => false
        );

		$parameters = array_filter( array_unique( array_merge( $parameters, $params) ) );

        return $this->makeRequest( $context, $parameters );
    }

	/**
     * Set the base URL for API endpoints
     *
     * @param string $url
     *
     * @return $this
     */
    public function setBaseUrl($url)
    {
        if (substr($url, -1) != '/') {
            $url .= '/';
        }

        if (substr($url,-4,4) != 'api/') {
            $url .= 'api/';
        }

        $this->baseUrl = $url;

        return $this;
    }

	/**
     * Returns array of HTTP response headers
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->parseHeaders($this->_httpResponseHeaders);
    }

    /**
     * Returns array of HTTP response headers
     *
     * @return array
     */
    public function getResponseInfo()
    {
        return $this->_httpResponseInfo;
    }

	/**
	 * Log messages
	 *
	 * @param  string $message [description]
	 * @return [type]          [description]
	 */
	protected function log( $message )
	{
		$this->logs[] = $message;
	}

	/**
     * @param       $url
     * @param array $headers
     * @param array $parameters
     * @param       $method
     * @param array $settings
     *
     * @return array
     */
    protected function prepareRequest($url, array $headers, array $parameters, $method, array $settings)
    {
        //Set Basic Auth parameters/headers
        $headers = array_merge($headers, array($this->buildAuthorizationHeader(), 'Expect:'));

        return array($headers, $parameters);
    }

	/**
     * Build header for Basic Authentication
     *
     * @return string
     */
    private function buildAuthorizationHeader()
    {
        /*
        |--------------------------------------------------------------------------
        | Authorization Header
        |--------------------------------------------------------------------------
        |
        | Authorization is passed in the Header using Basic Authentication.
        |
        | Basically we take the username and password and seperate it with a
        | colon (:) and base 64 encode it:
        |
        |   'Authorization: Basic username:password'
        |
        |   ==> with base64 encoding of the username and password
        |
        |   'Authorization: Basic dXNlcjpwYXNzd29yZA=='
        |
        */
        return 'Authorization: Basic ' . base64_encode($this->userName.':'.$this->password);
    }

	/**
	 * Send API request
	 *
	 * @param  string $context The type of context we are requesting from the host
	 * @param  array  $values  Request params
	 * @return json|array
	 */
	public function makeRequest($context, array $parameters = array(), $method = 'GET', array $settings = array())
    {
    	$url = $this->baseUrl . $context;
		list($url, $parameters) = $this->separateUrlParams($url, $parameters);

        //make sure $method is capitalized for congruency
        $method  = strtoupper($method);
        $headers = (isset($settings['headers']) && is_array($settings['headers'])) ? $settings['headers'] : array();

        list($headers, $parameters) = $this->prepareRequest($url, $headers, $parameters, $method, $settings);

        //Set default CURL options
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true
        );

        // CURLOPT_FOLLOWLOCATION cannot be activated when an open_basedir is set
        $options[CURLOPT_FOLLOWLOCATION] = (ini_get('open_basedir')) ? false : true;

        //Set custom REST method if not GET or POST
        if (!in_array($method, array('GET', 'POST'))) {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }

        //Set post fields for POST/PUT/PATCH requests
        $isPost = false;
        if (in_array($method, array('POST', 'PUT', 'PATCH'))) {
            $isPost = true;
            $parameters = http_build_query($parameters, '', '&');
            $options[CURLOPT_POST]       = true;
            $options[CURLOPT_POSTFIELDS] = $parameters;

            $this->log('Posted parameters = '.print_r($parameters, true));
        }

        $query = $this->getQueryParameters($isPost, $parameters);
        $this->log('Query parameters = '.print_r($query, true));

        //Create a query string for GET/DELETE requests
        if (count($query) > 0) {
            $queryGlue = strpos($url, '?') === false ? '?' : '&';
            $url       = $url.$queryGlue.http_build_query($query);
            $this->log('URL updated to '.$url);
        }

        // Set the URL
        $options[CURLOPT_URL] = $url;

        $headers[]                   = 'Accept: application/json';
        $options[CURLOPT_HTTPHEADER] = $headers;

        //Make CURL request
        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response               	= curl_exec($curl);
        $responseArray          	= explode("\r\n\r\n", $response);
        $body                   	= array_pop($responseArray);
        $this->_httpResponseHeaders = implode("\r\n\r\n", $responseArray);
        $this->_httpResponseInfo 	= curl_getinfo($curl);

        curl_close($curl);

		// Set response
        // $return = new Response(array(
        //     'code' => $httpResponseCode,
        //     'response' => $response
        // ));

        $responseGood = false;

        //Check to see if the response is JSON
        $parsed = json_decode($body, true);

        if ($parsed === null) {
            if (strpos($body, '=') !== false) {
                parse_str($body, $parsed);
                $responseGood = true;
            }
        } else {
            $responseGood = true;
        }

        //Show error when http_code is not appropriate
        if (!in_array($this->_httpResponseInfo['http_code'], array(200, 201))) {
            if ($responseGood) {
                return $parsed;
            }

            throw new Exception($body);
        }

        return ($responseGood) ? $parsed : $body;
    }

	/**
     * @param $isPost
     * @param $parameters
     *
     * @return array
     */
    protected function getQueryParameters($isPost, $parameters)
    {
        return ($isPost) ? array() : $parameters;
    }

	/**
     * Separates parameters from base URL
     *
     * @param $url
     * @param $params
     *
     * @return array
     */
    protected function separateUrlParams($url, $params)
    {
        $a = parse_url($url);

        if (!empty($a['query'])) {
            parse_str($a['query'], $qparts);
            $cleanParams = array();
            foreach ($qparts as $k => $v) {
                $cleanParams[$k] = $v ? $v : '';
            }
            $params   = array_merge($params, $cleanParams);
            $urlParts = explode('?', $url, 2);
            $url      = $urlParts[0];
        }

        return array($url, $params);
    }
}
