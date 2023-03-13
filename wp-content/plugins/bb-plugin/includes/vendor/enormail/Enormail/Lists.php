<?php namespace Enormail;
/**
* Communicates with the Enormail Lists API
* 
* @author Enormail
* @version 1.0
* @docs http://developers.enormail.eu/#api-account
*/
class Lists extends Base {
    
    /**
    * Retrieves a list of lists
    *
    * @access public
    * @param integer $page pagenumber from resultset
    * @param string $sortby the column name to sort on
    * @param string $order the order of the results (asc|desc)
    * @return string (json|xml)
    */
    public function get($page = 1, $sortby = 'weight', $order = 'asc')
    {
        return $this->rest->get("/lists.{$this->format}", array(
            'page' => $page, 
            'sortby' => $sortby, 
            'order' => $order
        ));
    }
    
    /**
    * Retrieves the details of a single list
    *
    * @access public
    * @param string $listid the unique listid string
    * @return string (json|xml)
    */
    public function details($listid)
    {
        return $this->rest->get("/lists/{$listid}.{$this->format}");
    }
    
    /**
    * Creates a new list
    *
    * @access public
    * @param string $title the title of the new list
    * @param integer $notify_on_subscribe wether to send a notification 
    * e-mail when a new contact subscribed
    * @param string the e-mail address to send the notification message to
    * @return string (json|xml)
    */
    public function create($title, $notify_on_subscribe = 0, $mail_notify_to = null)
    {
        return $this->rest->post("/lists.{$this->format}", array(
            'title' => $title,
            'notify_on_subscribe' => $notify_on_subscribe,
            'mail_notify_to' => $mail_notify_to
        ));
    }
    
    /**
    * Updates an existing list
    *
    * @access public
    * @param string $listid the unique listid string
    * @param string $title the title of the new list
    * @param integer $notify_on_subscribe wether to send a notification 
    * e-mail when a new contact subscribed
    * @param string the e-mail address to send the notification message to
    * @return string (json|xml)
    */
    public function update($listid, $title, $notify_on_subscribe = 0, $mail_notify_to = null)
    {
        return $this->rest->put("/lists/{$listid}.{$this->format}", array(
            'title' => $title,
            'notify_on_subscribe' => $notify_on_subscribe,
            'mail_notify_to' => $mail_notify_to
        ));
    }
    
    /**
    * Deletes a list with all the contacts
    *
    * @access public
    * @param string $listid the unique listid string
    * @return string results (json|xml)
    */
    public function delete($listid)
    {
        return $this->rest->delete("/lists/{$listid}.{$this->format}");
    }
    
}
?>