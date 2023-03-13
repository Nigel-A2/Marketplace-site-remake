<?php namespace Enormail;
/**
* Communicates with the Enormail Forms API
* 
* @author Enormail
* @version 1.0
* @docs http://developers.enormail.eu/#api-forms
*/
class Forms extends Base {
    
    /**
    * Fetches a list of forms
    *
    * @access public
    * @param integer $page pagenumber from resultset
    * @return string (json|xml)
    */
    public function get()
    {
        return $this->rest->get("/forms.{$this->format}");
    }
    
    /**
    * Retrieves a form's details
    *
    * @access public
    * @param string $formid the form id
    * @return string (json|xml)
    */
    public function details($formid)
    {
        return $this->rest->get("/forms/{$formid}.{$this->format}");
    }
    
    /**
    * Retrieves the form html
    *
    * @access public
    * @param string $formid the form id
    * @param string $format (At the moment only html is supported)
    * @return string (json|xml)
    */
    public function embed($formid, $format = 'html')
    {
        return $this->rest->get("/forms/{$formid}/{$format}.{$this->format}");
    }
    
    /**
    * Emulates the form like it was posted by a new subscriber
    *
    * @access public
    * @param string $formid the form id
    * @param string $name the contact's name (fullname or name)
    * @param string $email the contact's e-mail address
    * @param array $fields an array with optional fields, example: array('lastname' => 'Contact lastname', 'city' => 'City name')
    * @return string (json|xml)
    */
    public function subscribe($formid, $name, $email, $fields = array())
    {
        return $this->rest->post("/forms/{$formid}/subscribe.{$this->format}", array(
            'name' => $name,
            'email' => $email,
            'fields' => $fields
        ));
    }
    
}