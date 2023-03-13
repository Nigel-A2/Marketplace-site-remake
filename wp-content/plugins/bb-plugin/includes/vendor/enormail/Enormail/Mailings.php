<?php namespace Enormail;
/**
* Communicates with the Enormail Mailings API
* 
* @author Enormail
* @version 1.0
* @docs http://developers.enormail.eu/#api-mailings
*/
class Mailings extends Base {
    
    /**
    * Retrieves a list of mailings from your account
    *
    * @access public
    * @param string $type the mailing type (sent, scheduled, draft)
    * @param integer $page pagenumber from resultset
    * @return string (json|xml)
    */
    public function get($type, $page = 1)
    {
        return $this->rest->get("/mailings/{$type}.{$this->format}", array('page' => $page));
    }
    
    /**
    * Retrieves details of a single mailing
    *
    * @access public
    * @param string $mailingid the unique mailingid
    * @return string (json|xml)
    */
    public function details($mailingid)
    {
        return $this->rest->get("/mailings/{$mailingid}.{$this->format}");
    }
    
    /**
    * Retrieves the mailing statistics from a sent mailing
    *
    * @access public
    * @param string $mailingid the unique mailingid
    * @return string (json|xml)
    */
    public function stats($mailingid)
    {
        return $this->rest->get("/mailings/{$mailingid}/stats.{$this->format}");
    }
    
    /**
    * Creates a new draft mailing
    *
    * @access public
    * @param array $mailing array(
    *                           'format' => (string) {html | plaintext}, 
    *                           'subject' => (string) {Mailing subject},
    *                           'body' => (string) {Mailing body},
    *                           'fromname' => (string) {Sender name},
    *                           'fromemail' => (string) {Sender e-mail address},
    *                           'replyto' => (string) {Reply to e-mail address},
    *                           'track_mail_open' => (int) {1 or 0 default: 1},
    *                           'track_link_click' => (int) {1 or 0 default: 1},
    *                           'track_google_analytics' => (int) {1 or 0 default: 0},
    *                           'google_analytics_campaign' => (string) GA campaign name (default: enormail)
    *                        )
    * @return string (json|xml)
    */
    public function create($mailing)
    {
        return $this->rest->post("/mailings.{$this->format}", $mailing);
    }
    
    /**
    * Sends a draft mailing to the posted lists
    *
    * @access public
    * @param string $mailingid the mailingid of the mailing to send
    * @param array $lists listid's to send to: array(
    *                                           '(string) {listid}',
    *                                           '(string) {listid}'
    *                                           )
    * @param string send time, use 'now' to send immediately or timestamp to schedule (format: YYYYMMDDHHIISS) 
    * @return string (json|xml)
    */
    public function send($mailingid, $lists, $schedule_at = 'now')
    {
        return $this->rest->post("/mailings/{$mailingid}/send.{$this->format}", array('lists' => $lists, 'schedule_at' => $schedule_at));
    }
    
    /**
    * Unschedules a scheduled mailing.
    *
    * @access public
    * @param string $mailingid the mailingid of the mailing to unschedule
    * @return string (json|xml)
    */
    public function unschedule($mailingid)
    {
        return $this->rest->post("/mailings/{$mailingid}/unschedule.{$this->format}");
    }
    
    /**
    * Deletes a draft campaign from your account.
    *
    * @access public
    * @param string $mailingid the mailingid of the mailing to unschedule
    * @return string (json|xml)
    */
    public function delete($mailingid)
    {
        return $this->rest->delete("/mailings/{$mailingid}.{$this->format}");
    }
    
}