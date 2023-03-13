<?php
/**
 * @name iContactProApi
 * @package iContactPro
 * @author iContact <www.icontact.com>
 * @description This class is a wrapper for the iContactPro API.
 * It makes integrating iContactPro into your app as simple as
 * calling a method.
 * @version 2.0
**/
class iContactProApi {

	//////////////////////////////////////////////////////////////////////////////
	/// Properties //////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////

	protected static $oInstance  = null;    // This holds the instance of this class
	protected $iCompanyId        = null;    // This holds the company ID
	protected $iProfileId        = null;    // This holds the profile ID
	protected $aConfig           = array(); // This is our array for pragmatically overriding configuration constants
	protected $aErrors           = array(); // This holds the errors encountered with the iContactPro API
	protected $sLastRequest      = null;    // This holds the last request JSON
	protected $sLastResponse     = null;    // This holds the last response JSON
	protected $sRequestUri       = null;    // This stores the last used URL
	protected $aSearchParameters = array(); // This is our container for search params
	protected $iTotal            = 0;       // If the results return a total, it will be stored here
	protected $aWarnings         = array(); // This holds the warnings encountered with the iContactPro API

	//////////////////////////////////////////////////////////////////////////////
	/// Singleton ///////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////

	/**
	 * This sets the singleton pattern instance
	 * @static
	 * @access public
	 * @param iContactProApi $oInstance Instance to set to
	 * @return iContactProApi $this
	**/
	public static function setInstance($oInstance) {

		self::$oInstance = $oInstance;
		// Return instance of class
		return self::$oInstance;
	}

	/**
	 * This gets the singleton instance
	 * @static
	 * @access public
	 * @return iContactProApi $this
	**/
	public static function getInstance() {
		// Check to see if an instance has already
		// been created
		if (is_null(self::$oInstance)) {
			// If not, return a new instance
			self::$oInstance = new self();
			return self::$oInstance;
		} else {
			// If so, return the previously created
			// instance
			return self::$oInstance;
		}
	}

	/**
	 * This resets the singleton instance to null
	 * @static
	 * @access public
	 * @return void
	**/
	public static function resetInstance() {
		// Reset the instance
		self::$oInstance = null;
	}

	//////////////////////////////////////////////////////////////////////////////
	/// Constructor /////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////

	/**
	 * This is our constuctor and simply checks for
	 * defined constants and configuration values and
	 * then builds the configuration from that
	 * @access protected
	 * @return iContactProApi $this
	**/
	protected function __construct() {
		// Check for constants
		$aConstantMap = array(
			// 'ICP_APIVERSION',
			'ICP_APPID'       => 'appId',
			// 'ICP_APIURL',
			'ICP_APIUSERNAME' => 'apiUsername',
			'ICP_APIPASSWORD' => 'apiPassword',
			'ICP_COMPANYID' => 'companyId',
			'ICP_PROFILEID' => 'profileId'
		);
		// Loop through the map
		foreach ($aConstantMap as $sConstant => $sConfigKey) {
			// Check for the defined constant
			if (defined($sConstant)) {
				// Set the configuration key to the contant's value
				$this->aConfig[$sConfigKey] = constant($sConstant);
			}
		}
		// Return instance
		return $this;
	}

	//////////////////////////////////////////////////////////////////////////////
	/// Public //////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////

	/**
	 * This method adds a contact to your iContactPro account
	 * @access public
	 * @param string $sEmail
	 * @param string [$sStatus]
	 * @param string [$sPrefix]
	 * @param string [$sFirstName]
	 * @param string [$sLastName]
	 * @param string [$sSuffix]
	 * @param string [$sStreet]
	 * @param string [$sStreet2]
	 * @param string [$sCity]
	 * @param string [$sState]
	 * @param string [$sPostalCode]
	 * @param string [$sPhone]
	 * @param string [$sFax]
	 * @param string [$sBusiness]
	 *
	 * @return object
	**/
	public function addContact($sEmail, $sStatus = 'normal', $sPrefix = null, $sFirstName = null, $sLastName = null, $sSuffix = null, $sStreet = null, $sStreet2 = null, $sCity = null, $sState = null, $sPostalCode = null, $sPhone = null, $sFax = null, $sBusiness = null) {
		// Valid statuses
		$aValidStatuses = array('normal', 'bounced', 'donotcontact', 'pending', 'invitable', 'deleted');
		// Contact placeholder
		$aContact       = array(
			'email' => $sEmail
		);
		// Check for a prefix
		if (!empty($sPrefix)) {
			// Add the new prefix
			$aContact['prefix'] = (string) $sPrefix;
		}
		// Check for a first name
		if (!empty($sFirstName)) {
			// Add the new first name
			$aContact['firstName'] = (string) $sFirstName;
		}
		// Check for a last name
		if (!empty($sLastName)) {
			// Add the new last name
			$aContact['lastName'] = (string) $sLastName;
		}
		// Check for a suffix
		if (!empty($sSuffix)) {
			// Add the new suffix
			$aContact['suffix'] = (string) $sSuffix;
		}
		// Check for a street
		if (!empty($sStreet)) {
			// Add the new street
			$aContact['street'] = (string) $sStreet;
		}
		// Check for a street2
		if (!empty($sStreet2)) {
			// Add the new street 2
			$aContact['street2'] = (string) $sStreet2;
		}
		// Check for a city
		if (!empty($sCity)) {
			// Add the new city
			$aContact['city'] = (string) $sCity;
		}
		// Check for a state
		if (!empty($sState)) {
			// Add the new state
			$aContact['state'] = (string) $sState;
		}
		// Check for a postal code
		if (!empty($sPostalCode)) {
			// Add the new postal code
			$aContact['postalCode'] = (string) $sPostalCode;
		}
		// Check for a phone number
		if (!empty($sPhone)) {
			// Add the new phone number
			$aContact['phone'] = (string) $sPhone;
		}
		// Check for a fax number
		if (!empty($sFax)) {
			// Add the new fax number
			$aContact['fax'] = (string) $sFax;
		}
		// Check for a business name
		if (!empty($sBusiness)) {
			// Add the new business
			$aContact['business'] = (string) $sBusiness;
		}
		// Check for a valid status
		if (!empty($sStatus) && in_array($sStatus, $aValidStatuses)) {
			// Add the new status
			$aContact['status'] = $sStatus;
		} else {
			$aContact['status'] = 'normal';
		}

		// Make the call
		//echo("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/contacts");
		$aContacts = $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/contacts", 'POST', array($aContact), 'contacts');
		// Return the contact
		return $aContacts[0];
	}

	/**
	 * This method adds a custom field or "term"
	 * to the array of search parameters
	 * @access public
	 * @param string $sName
	 * @param string $sValue
	 * @return iContactProApi $this
	**/
	public function addCustomQueryField($sName, $sValue) {
		// Add the field
		$this->aSearchParameters[$sName] = (string) $sValue;
		// Return instance
		return $this;
	}

	/**
	 * This message adds a list to your iContactPro account
	 * @access public
	 * @param string $sName
	 * @param integer $iWelcomeMessageId
	 * @param bool [$bEmailOwnerOnChange]
	 * @param bool [$bWelcomeOnManualAdd]
	 * @param bool [$bWelcomeOnSignupAdd]
	 * @param string [$sDescription]
	 * @param string [$sPublicName]
	 * @return object
	**/
	public function addList($sName, $iWelcomeMessageId, $bEmailOwnerOnChange = true, $bWelcomeOnManualAdd = false, $bWelcomeOnSignupAdd = false, $sDescription = null, $sPublicName = null) {
		// Setup the list
		$aList = array(
			'name'               => $sName,
			'welcomeMessageId'   => $iWelcomeMessageId,
			'emailOwnerOnChange' => intval($bEmailOwnerOnChange),
			'welcomeOnManualAdd' => intval($bWelcomeOnManualAdd),
			'welcomeOnSignupAdd' => intval($bWelcomeOnSignupAdd),
			'description'        => $sDescription,
			'publicname'         => $sPublicName
		);
		// Make the call
		$aLists = $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/lists", 'POST', array($aList), 'lists');
		// Return the list
		return $aLists[0];
	}

	/**
	 * This method adds a message to
	 * your iContactPro API account
	 * @access public
	 * @param string $sSubject
	 * @param integer $iCampaignId
	 * @param string [$sHtmlBody]
	 * @param string [$sTextBody]
	 * @param string [$sMessageName]
	 * @param integer [$iListId]
	 * @param string [$sMessageType]
	 * @return object
	**/
	public function addMessage($sSubject, $iCampaignId, $sHtmlBody = null, $sTextBody = null, $sMessageName = null, $iListId = null, $sMessageType = 'normal') {
		// Valid message types
		$aValidMessageTypes = array('normal', 'autoresponder', 'welcome', 'confirmation');
		// Setup the message data
		$aMessage           = array(
			'campaignId'  => $iCampaignId,
			'htmlBody'    => $sHtmlBody,
			'messageName' => $sMessageName,
			'messageType' => (in_array($sMessageType, $aValidMessageTypes) ? $sMessageType : 'normal'),
			'subject'     => $sSubject,
			'textBody'    => $sTextBody
		);
		// Add the message
		$aNewMessage = $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/messages", 'POST', array($aMessage), 'messages');
		// Return the message data
		return $aNewMessage[0];
	}

	/**
	 * This method adds a field to the order by
	 * key in the search parameters array
	 * @access public
	 * @param string $sField
	 * @param string [$sDirection]
	 * @return iContactProApi $this
	**/
	public function addOrderBy($sField, $sDirection = null) {
		// Check for existing order by parameters
		if (empty($this->aSearchParameters['orderby'])) {
			// Check for a direction
			if (empty($sDirection)) {
				// Add just the field
				$this->aSearchParameters['orderby'] = (string) $sField;
			} else {
				// Add the field and direction
				$this->aSearchParameters['orderby'] = (string) "{$sField}:{$sDirection}";
			}
		} else {
			// Check for a direction
			if (empty($sDirection)) {
				// Append just the field
				$this->aSearchParameters['orderby'] .= (string) ",{$sField}";
			} else {
				// Append the field and direction
				$this->aSearchParameters['orderby'] .= (string) ",{$sField}:{$sDirection}";
			}
		}
		// Return failure
		return false;
	}

	/**
	 * This method handles the deleting of a single list
	 * @access public
	 * @param integer $iListId
	 * @return bool
	**/
	public function deleteList($iListId) {
		// Delete the list
		return $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/lists/{$iListId}", 'delete');
	}

	/**
	 * This method handles the handshaking between this app and the iContactPro API
	 * @access public
	 * @param string $sResource
	 * @param string $sMethod
	 * @param string $sReturnKey
	 * @param mixed  $mPostData Array, object, or string
	 * @return array|object
	**/
	public function makeCall($sResource, $sMethod = 'GET', $mPostData = null, $sReturnKey = null) {
		// List of needed constants
		$aRequiredConfigs = array('apiPassword', 'apiUsername', 'appId', 'companyId', 'profileId');

		foreach ($aRequiredConfigs as $sKey) {
			if (empty($this->aConfig[$sKey])) {
				$this->addError("{$sKey} is undefined.");
			}
		}

		$sApiUrl    = (string) "{$this->getUrl()}{$sResource}";
		$rHandle     = curl_init();
		curl_setopt($rHandle, CURLOPT_HTTPHEADER, $this->getHeaders());
		curl_setopt($rHandle, CURLOPT_RETURNTRANSFER, true);
		if (defined('ICP_NOVERIFY') && constant('ICP_NOVERIFY')) {
			// conditionally disable SSL certificate verification
			curl_setopt($rHandle, CURLOPT_SSL_VERIFYPEER, false);
		}
		// Determine the request
		// method we are using
		switch (strtoupper($sMethod)) {
			// Deleting data
			case 'DELETE' :
				curl_setopt($rHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
			break;
			// Recieving data
			case 'GET'    :
				if (!empty($this->aSearchParameters)) {
					$sApiUrl .= (string) '?'.http_build_query($this->aSearchParameters);
				}
			break;
			// Sending data
			case 'POST'   :
				if (empty($mPostData)) {
					$this->addError('No POST data was provided.');
				} else {
					curl_setopt($rHandle, CURLOPT_POST, true);
					curl_setopt($rHandle, CURLOPT_POSTFIELDS, json_encode($mPostData));
					$this->sLastRequest = (string) json_encode($mPostData);
				}
			break;
			// Uploading data
			case 'PUT'    :
				if (empty($mPostData)) {
					$this->addError('No file or data specified for PUT request');
				} elseif (!is_string($mPostData) || !file_exists($mPostData)) {
					// Not a file, so we assume this is just data
					curl_setopt($rHandle, CURLOPT_CUSTOMREQUEST, "PUT");
					curl_setopt($rHandle, CURLOPT_POSTFIELDS, $mPostData);
				} else {
					$rFileContentHandle = fopen($mPostData, 'r');
					if ($rFileContentHandle === false) {
						$this->addError('A non-existant file was specified for POST data, or the file could not be opened.');
					} else {
						// Found a file, so upload its contents
						curl_setopt($rHandle, CURLOPT_PUT, true);
						curl_setopt($rHandle, CURLOPT_INFILE, $rFileContentHandle);
					}
				}
			break;
		}
		// Store the URL into the instance
		$this->sRequestUri = (string) $sApiUrl;
		// Give our handle a URL
		curl_setopt($rHandle, CURLOPT_URL, $sApiUrl);
		// Try to execute the handle
		if (!$sResponse = curl_exec($rHandle)) {
			// Add an error, for we could
			// not even execute the handle
			$this->addError('We were unable to execute the cURL handle.');
		}
		// Set the response JSON
		$this->sLastResponse = (string) $sResponse;
		// Try to decode the response
		if ((!$aResponse = json_decode($sResponse)) && (strtoupper($sMethod) != 'DELETE')) {
			// Add an error, for the API
			// did not return valid JSON
			$this->addError('The iContactPro API did not return valid JSON.');
		}
		curl_close($rHandle);
		// Check for errors from the API
		if (!empty($aResponse->errors)) {
			// Loop through the errors
			foreach ($aResponse->errors as $sError) {
				// Add the error
				$this->addError($sError, 1);
			}
		}
		// Check for warnings from the API
		if (!empty($aResponse->warnings)) {
			// Loop through the warnings
			foreach ($aResponse->warnings as $sWarning) {
				// Add the warning
				$this->addWarning($sWarning);
			}
		}
		// Check for set errors
		if (!empty($this->aErrors)) {
			throw new Exception('Errors have occurred and the system cannot continue.  Use getErrors() for details.');
		}
		// Check for a total
		if (!empty($aResponse->total)) {
			$this->iTotal = (integer) $aResponse->total;
		}
		// Return the response
		if (strtoupper($sMethod) == 'DELETE') {
			return true;
		} elseif (empty($sReturnKey)) {
			return $aResponse;
		} else {
			// Return the narrowed resposne
			return $aResponse->$sReturnKey;
		}
	}

	/**
	 * This method sends a message
	 * @access public
	 * @param string $sIncludeListId
	 * @param integer $iMessageId
	 * @param string [$sExcludeListIds]
	 * @param string [$sExcludeSegmentIds]
	 * @param string [$sIncludeSegmentIds]
	 * @param string [$sScheduledTime]
	 * @return object
	**/
	public function sendMessage($sIncludeListIds, $iMessageId, $sExcludeListIds = null, $sExcludeSegmentIds = null, $sIncludeSegmentIds = null, $sScheduledTime = null) {
		// Send the message
		$aSends = $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/sends", 'POST', array(
			array(
				'excludeListIds'    => $sExcludeListIds,
				'excludeSegmentIds' => $sExcludeSegmentIds,
				'includeListIds'    => $sIncludeListIds,
				'includeSegmentIds' => $sIncludeSegmentIds,
				'scheduledTime'     => (empty($sScheduledTime) ? null : date('c', strtotime($sScheduledTime)))
			)
		), 'sends');
		return $aSends;
	}

	/**
	 * This method subscribes a contact to a list
	 * @access public
	 * @param integer $iContactId
	 * @param integer $iListId
	 * @param string  $sStatus
	 * @return object
	 **/
	public function subscribeContactToList($iContactId, $iListId, $sStatus = 'normal') {
		// Valid statuses
		$aValidStatuses = array('normal', 'pending', 'unsubscribed');
		// Setup the subscription and make the call
		$aSubscriptions = $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/subscriptions", 'POST', array(
			array(
				'contactId' => $iContactId,
				'listId'    => $iListId,
				'status'    => $sStatus
			)
		), 'subscriptions');
		return $aSubscriptions;
	}

	/**
	 * This method updates a contact in your iContactPro account
	 * @access public
	 * @param integer $iContactId
	 * @param string  $sEmail
	 * @param string  $sPrefix
	 * @param string  $sFirstName
	 * @param string  $sLastName
	 * @param string  $sSuffix
	 * @param string  $sStreet
	 * @param string  $sStreet2
	 * @param string  $sCity
	 * @param string  $sState
	 * @param string  $sPostalCode
	 * @param string  $sPhone
	 * @param string  $sFax
	 * @param string  $sBusiness
	 * @param string  $sStatus
	 * @return bool|object
	 **/
	public function updateContact($iContactId, $sEmail = null, $sPrefix = null, $sFirstName = null, $sLastName = null, $sSuffix = null, $sStreet = null, $sStreet2 = null, $sCity = null, $sState = null, $sPostalCode = null, $sPhone = null, $sFax = null, $sBusiness = null, $sStatus = null) {
		// Valid statuses
		$aValidStatuses = array('normal', 'bounced', 'donotcontact', 'pending', 'invitable', 'deleted');
		// Contact placeholder
		$aContact       = array();
		// Check for an email address
		if (!empty($sEmail)) {
			// Add the new email
			$aContact['email'] = (string) $sEmail;
		}
		// Check for a prefix
		if (!empty($sPrefix)) {
			// Add the new prefix
			$aContact['prefix'] = (string) $sPrefix;
		}
		// Check for a first name
		if (!empty($sFirstName)) {
			// Add the new first name
			$aContact['firstName'] = (string) $sFirstName;
		}
		// Check for a last name
		if (!empty($sLastName)) {
			// Add the new last name
			$aContact['lastName'] = (string) $sLastName;
		}
		// Check for a suffix
		if (!empty($sSuffix)) {
			// Add the new suffix
			$aContact['suffix'] = (string) $sSuffix;
		}
		// Check for a street
		if (!empty($sStreet)) {
			// Add the new street
			$aContact['street'] = (string) $sStreet;
		}
		// Check for a street2
		if (!empty($sStreet2)) {
			// Add the new street 2
			$aContact['street2'] = (string) $sStreet2;
		}
		// Check for a city
		if (!empty($sCity)) {
			// Add the new city
			$aContact['city'] = (string) $sCity;
		}
		// Check for a state
		if (!empty($sState)) {
			// Add the new state
			$aContact['state'] = (string) $sState;
		}
		// Check for a postal code
		if (!empty($sPostalCode)) {
			// Add the new postal code
			$aContact['postalCode'] = (string) $sPostalCode;
		}
		// Check for a phone number
		if (!empty($sPhone)) {
			// Add the new phone number
			$aContact['phone'] = (string) $sPhone;
		}
		// Check for a fax number
		if (!empty($sFax)) {
			// Add the new fax number
			$aContact['fax'] = (string) $sFax;
		}
		// Check for a business name
		if (!empty($sBusiness)) {
			// Add the new business
			$aContact['business'] = (string) $sBusiness;
		}
		// Check for a valid status
		if (!empty($sStatus) && in_array($sStatus, $aValidStatuses)) {
			// Add the new status
			$aContact['status'] = $sStatus;
		}
		// Make sure the contact isn't empty
		if (!empty($aContact)) {
			// Make the call
			$oContact = $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/contacts/{$iContactId}", 'POST', array($aContact), 'contact');
			// Return the contact
			return $oContact;
		}
		// Inevitably return failure
		return false;
	}

	/**
	 * This method uploads a CSV file to the iContactPro API
	 * @access public
	 * @param string $sFile
	 * @param integer [$iListId]
	 * @param integer [$iUploadId]
	 * @return string|bool
	**/
	public function uploadData($sFile, $iListId = null, $iUploadId = null) {
		// Check for an upload ID
		if (empty($iUploadId)) {
			// Make the call
			$aUploads = $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/uploads", 'POST', array(
				array(
					'action' => 'add',
					'listIds' => $iListId
				)
			), 'uploads');
			// Store the uploadID
			$iUploadId = $aUploads[0]->uploadId;
		}
		// Upload the data
		if ($this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/uploads/{$iUploadId}/data", 'PUT', $sFile, 'uploadId')) {
			// Loop until the upload is complete
			while (true) {
				// Grab the upload
				$aUpload = $this->getUpload($iUploadId);
				// Check to see if the upload
				// has finished uploading
				if ($aUpload->status != 'receiving') {
					// Return the upload
					return $this->makeCall("/a/{$this->getCompanyId()}/c{$this->getProfileId()}/uploads/{$iUploadId}/data", 'GET');
				}
			}
		}
		// Return failure
		return false;
	}

	/**
	 * This message updates a list on your iContactPro account
	 * @access public
	 * @param string $sName
	 * @param integer $iListId
	 * @param string $sName
	 * @param integer $iWelcomeMessageId
	 * @param bool [$bEmailOwnerOnChange]
	 * @param bool [$bWelcomeOnManualAdd]
	 * @param bool [$bWelcomeOnSignupAdd]
	 * @param string [$sDescription]
	 * @param string [$sPublicName]
	 * @return object
	**/
	public function updateList($iListId, $sName, $iWelcomeMessageId, $bEmailOwnerOnChange = true, $bWelcomeOnManualAdd = false, $bWelcomeOnSignupAdd = false, $sDescription = null, $sPublicName = null) {
		// Setup the list
		$aList = array(
			'name'               => $sName,
			'welcomeMessageId'   => $iWelcomeMessageId,
			'emailOwnerOnChange' => intval($bEmailOwnerOnChange),
			'welcomeOnManualAdd' => intval($bWelcomeOnManualAdd),
			'welcomeOnSignupAdd' => intval($bWelcomeOnSignupAdd),
			'description'        => $sDescription,
			'publicname'         => $sPublicName
		);
		// Return the list
		return $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/lists/{$iListId}", 'POST', $aList, 'list');;
	}


	//////////////////////////////////////////////////////////////////////////////
	/// PROTECTED ///////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////

	/**
	 * This method appends an error to the list
	 * of errors encountered with the iContactPro API
	 * @access protected
	 * @param string $sText
	 * @param integer [$iCode]
	 * @return iContactProApi $this
	**/
	protected function addError($sText) {
		// Append the error
		array_push($this->aErrors, $sText);
		// Return instance
		return $this;
	}

	/**
	 * This method appends a warning to the list
	 * of warnings encountered with the iContactPro API
	 * @access protected
	 * @param string $sText
	 * @return iContactProApi $this
	**/
	public function addWarning($sText) {
		// Append the warning
		array_push($this->aWarnings, $sText);
		// Return instance
		return $this;
	}

	//////////////////////////////////////////////////////////////////////////////
	/// Getters /////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////

	/**
	 * This method grabs the campaigns associated
	 * your iContactPro account
	 * @access public
	 * @return object
	**/
	public function getCampaigns() {
		// Make the call and return the data
		return $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/campaigns", 'GET');
	}

	/**
	 * This method returns the companyId.  If the ID has
	 * not been set, it sets it from the configs.
	 *
	 * @access public
	 * @return integer
	 */
	public function getCompanyId() {
		if (empty($this->iCompanyId)) {
			$this->setCompanyId();
		}

		return $this->iCompanyId;
	}

	/**
	 * This method grabs a single contact
	 * from your iContactPro account
	 * @access public
	 * @param integer $iContactId
	 * @return object
	**/
	public function getContact($iContactId) {
		// Make the call and return the data
		return $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/contacts/{$iContactId}", 'GET', null, 'contact');
	}

	/**
	 * This method grabs the contacts associated
	 * with you iContactPro API account
	 * @access public
	 * @return array
	**/
	public function getContacts() {
		// Make the call and return the data
		return $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/contacts", 'GET');
	}

	/**
	 * This method returns any set
	 * errors in the current instance
	 * @access public
	 * @return array|bool
	**/
	public function getErrors() {
		// Check for errors
		if (empty($this->aErrors)) {
			return false;
		} else {
			return $this->aErrors;
		}
	}

	/**
	 * This method builds the header array
	 * for making calls to the API
	 * @access public
	 * @return array
	**/
	public function getHeaders() {
		// Return the headers
		return array(
			'Except:',
			'Accept:  application/json',
			'Content-type:  application/json',
			'Api-Version:  ' . (defined('ICP_APIVERSION')       ? constant('ICP_APIVERSION') : '2.2'),
			'Api-AppId:  '   . (!empty($this->aConfig['appId'])       ? $this->aConfig['appId']         : (defined('ICP_APPID') ? constant('ICP_APPID') : '')),
			'Api-Username:  '. (!empty($this->aConfig['apiUsername']) ? $this->aConfig['apiUsername']   : (defined('ICP_APIUSERNAME') ? constant('ICP_APIUSERNAME') : '')),
			'Api-Password:  '. (!empty($this->aConfig['apiPassword']) ? $this->aConfig['apiPassword']   : (defined('ICP_APIPASSWORD') ? constant('ICP_APIPASSWORD') : ''))
		);
	}

	/**
	 * This method returns the last
	 * API POST request JSON
	 * @access public
	 * @param bool [$bDecode]
	 * @return string|object
	**/
	public function getLastRequest($bDecode = false) {
		if ($bDecode === true) {
			return json_decode($this->sLastRequest);
		}
		return $this->sLastRequest;
	}

	/**
	 * This method returns the last
	 * API response JSON
	 * @access public
	 * @param bool [$bDecode]
	 * @return string|object
	**/
	public function getLastResponse($bDecode = false) {
		if ($bDecode === true) {
			return json_decode($this->sLastResponse);
		}
		return $this->sLastResponse;
	}

	/**
	 * This method grabs a list of lists
	 * that are associated with you iContactPro
	 * API account
	 * @access public
	 * @return array
	**/
	public function getLists() {
		// Make the call and return the lists
		return $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/lists", 'GET', null, 'lists');
	}

	/**
	 * This method lists the opens of a
	 * single message based on the messageID
	 * @access public
	 * @param integer iMessageId
	 * @return integer
	**/
	public function getMessageOpens($iMessageId) {
		// Make the call and return the data
		return $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/messages/{$iMessageId}/opens", 'GET', null, 'total');
	}

	public function getMessages($sType = null) {
		// Check for a message type
		if (!empty($sType)) {
			$this->addCustomQueryField('messageType', $sType);
		}
		// Return the messages
		return $this->makeCall("/a/{$this->getCompanyId()}/c/{$this->getProfileId()}/messages", 'GET', null, 'messages');
	}

	/**
	 * This method returns the profileId.  If the ID has
	 * not been set, it sets it from the configs.
	 *
	 * @access public
	 * @return integer
	 */
	public function getProfileId() {
		if (empty($this->iProfileId)) {
			$this->setProfileId();
		}

		return $this->iProfileId;
	}

	/**
	 * This method returns the URL
	 * that the last API request
	 * called
	 * @access public
	 * @return string
	**/
	public function getRequestUri() {
		// Return the URL
		return $this->sRequestUri;
	}

	/**
	 * This method returns the count of the
	 * total number of records from the most
	 * recent API call, if there is one
	 * @access public
	 * @return integer
	**/
	public function getTotal() {
		// Return the total records
		return $this->iTotal;
	}

	/**
	 * This method simply returns the base URL for
	 * your API
	 * @access public
	 * @param bool [$bFull]
	 * @return string
	**/
	public function getUrl($bFull = false) {
		// Set the production URL
		$sBaseUrl     = defined('ICP_APIURL')        ? constant('ICP_APIURL')        : 'https://api.icpro.co/icp';
		// Do we need to return the entire url or just
		// the base url of the API service
		if ($bFull === false) {
			// Return the base url
			return $sBaseUrl;
		} else {
			// Return the base url and account details
			return $sBaseUrl . "/a/{$this->getCompanyId()}/c/{$this->getProfileId()}";
		}
	}

	/**
	 * This method grabs a specific upload
	 * @access public
	 * @param integer $iUploadId
	 * @return object
	**/
	public function getUpload($iUploadId) {
		// Return the upload data
		return $this->makeCall("/a/{$this->getCompanyId()}/c{$this->getProfileId()}/uploads/{$iUploadId}/data");
	}

	/**
	 * This method grabs the uploads associated
	 * with your iContactPro account
	 * @access public
	 * @return array
	**/
	public function getUploads() {
		// Return the uploads
		return $this->makeCall("/a/{$this->getCompanyId()}/c{$this->getProfileId()}/uploads");
	}

	/**
	 * This method returns the warnings encountered
	 * while communicating with the iContactPro API
	 * @access public
	 * @return array
	**/
	public function getWarnings() {
		// Return the current system warnings
		return $this->aWarnings;
	}

	//////////////////////////////////////////////////////////////////////////////
	/// Setters /////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////

	/**
	 * This method allows the companyId specified in the configs
	 * to be overridden
	 *
	 * @access public
	 * @param integer [$iCompanyId]
	 */
	public function setCompanyId($iCompanyId = null) {
		if (!empty ($iCompanyId)) {
			$this->iCompanyId = (integer) $iCompanyId;
		} else {
			$this->iCompanyId = (integer) (!empty($this->aConfig['companyId']) ? $this->aConfig['companyId']   : constant('ICP_COMPANYID'));
		}
	}

	/**
	 * This method sets configuration into the
	 * plugin to pragmatically override constants
	 * @access public
	 * @param array $aConfig
	 * @return iContactProApi $this
	**/
	public function setConfig($aConfig) {
		// Combine the arrays
		$this->aConfig = (array) array_merge($this->aConfig, $aConfig);
		// Return instance
		return $this;
	}

	/**
	 * This method sets the result limit
	 * for GET requests to the iContactPro API
	 * @access public
	 * @param integer $iLimit
	 * @return iContactProApi $this
	**/
	public function setLimit($iLimit) {
		// Set the limit in the search parameters
		$this->aSearchParameters['limit'] = (integer) $iLimit;
		// Return instance
		return $this;
	}

	/**
	 * This method sets the result index
	 * offset for paginating results from
	 * GET requests to the iContactPro API
	 * @access public
	 * @param integer $iOffset
	 * @return iContactProApi $this
	**/
	public function setOffset($iOffset) {
		// Set the offset in the search parameters
		$this->aSearchParameters['offset'] = (integer) $iOffset;
		// Return instance
		return $this;
	}

	/**
	 * This method allows the profileId specified in the configs
	 * to be overridden
	 *
	 * @access public
	 * @param integer [$iProfileId]
	 */
	public function setProfileId($iProfileId = null)
	{
		if (!empty ($iProfileId)) {
			$this->iProfileId = (integer) $iProfileId;
		} else {
			$this->iProfileId = (integer) (!empty($this->aConfig['profileId']) ? $this->aConfig['profileId']   : constant('ICP_PROFILEID'));
		}
	}
}