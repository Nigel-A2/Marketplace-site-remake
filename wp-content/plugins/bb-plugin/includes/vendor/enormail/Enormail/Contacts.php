<?php namespace Enormail;
/**
* The Enormail API wrapper contact class
* 
* @author Enormail
* @version 1.0
* @docs http://developers.enormail.eu/#api-contacts
*/
class Contacts extends Base {
    
    /**
    * Retrieves a list of contacts
    *
    * @access public
    * @param string $listid the unique listid string
    * @param string $state the state of the contacts (active, unconfirmed, unsubscribed, bounced)
    * @param integer $page the page number from the resultset
    * @param integer $pagesize the contacts par page size (max 250)
    * @return string results (json|xml)
    */
    public function get($listid, $state = 'active', $page = 1, $pagesize = 250)
    {
        return $this->rest->get("/contacts/{$listid}/{$state}.{$this->format}", array('page' => $page, 'pagesize' => $pagesize));
    }
    
    /**
    * Retrieves a contact's details
    *
    * @access public
    * @param string $listid the unique listid from the list the contact belongs to
    * @param string $email the contact's email address
    * @return string results (json|xml)
    */
    public function details($listid, $email)
    {
        return $this->rest->get("/contacts/{$listid}.{$this->format}", array('email' => $email));
    }
    
    /**
    * Adds a contact to the list
    *
    * @access public
    * @param string $listid the unique listid string
    * @param string $name the contact's name
    * @param string $email the contact's e-mail address
    * @param array $fields an array with optional fields, example: array('lastname' => 'Contact lastname', 'city' => 'City name')
    * @param integer $activate_autoresponder a flag to activate the autoresponder when the contact is added (1 or 0, default 1)
    * @return string results (json|xml)
    */
    public function add($listid, $name, $email, array $fields = null, $activate_autoresponder = 1)
    {
        return $this->rest->post("/contacts/{$listid}.{$this->format}", array(
            'name' => $name,
            'email' => $email,
            'fields' => $fields, 
            'activate_autoresponder' => $activate_autoresponder
        ));
    }
    
    /**
    * Updates an existing contact from a specified list
    *
    * @access public
    * @param string $listid the unique listid string
    * @param string $name the contact's name
    * @param string $email the contact's e-mail address
    * @param array $fields an array with optional fields, example: array('lastname' => 'Contact lastname', 'city' => 'City name')
    * @param string $new_email the contact's new e-email address (optional)
    * @param string $move_to_listid the list of the list you want the contact to move to (optional)
    * @return string results (json|xml)
    */
    public function update($listid, $name, $email, array $fields = null, $new_email = null, $move_to_listid = null)
    {
        $aData['name'] = $name;
        $aData['fields'] = $fields;
        
        if (!is_null($new_email))
            $aData['email'] = $new_email;
        
        if (!is_null($move_to_listid))
            $aData['listid'] = $move_to_listid;

        return $this->rest->put("/contacts/{$listid}.{$this->format}?email={$email}", $aData);
    }
    
    /**
    * Unsubscribes a contact from the specified list
    *
    * @access public
    * @param string $listid the unique listid string
    * @param string $email the contact's e-mail address
    * @return string results (json|xml)
    */
    public function unsubscribe($listid, $email)
    {
        return $this->rest->post("/contacts/{$listid}/unsubscribe.{$this->format}", array('email' => $email));
    }
    
    /**
    * Deletes a contact from the specified list
    *
    * @access public
    * @param string $listid the unique listid string
    * @param string $email the contact's e-mail address
    * @return string results (json|xml)
    */
    public function delete($listid, $email)
    {
        return $this->rest->delete("/contacts/{$listid}.{$this->format}", array('email' => $email));
    }
    
}
