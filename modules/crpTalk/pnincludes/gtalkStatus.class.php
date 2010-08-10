<?php
/**
 * There seems to be simple PHP API for retrieving a user's Google Talk status
 * Google does provide a badge, however, to post your status/images/links to your
 * website to start a chat. Using this code, we can extract the text status and
 * return that information to our PHP API
 *
 * Instructions :
 * 1- You must have fopen configured on your web server
 * 2- To create your chatback badge, visit http://www.google.com/talk/service/badge/New. If you're using a Google Apps account, you can create a chatback badge by visiting http://www.google.com/talk/service/a/DOMAIN/badge/New where DOMAIN is the name of your domain.
 * 3- Use the alphanumeric account hash to seed the constructor
 *
 * Example
 * $gtalkStatus = new gtalkStatus('z01q6amlqv8rcbf13vtojet2to2gpo18cbmr372r99m552h4ru1unq9j65sglu1gesv52s812q9ca2sketv6sdfo4d1c6hkgr7tkdk');
 * echo 'I am '.($gtalkStatus->isOnline()?'online':'offline').'<br>'.$gtalkStatus->getStatusMsg();
 *
 * Note: This class will NOT work for other Jabber/WMPP accounts.
 * Note: This class depends on the output from a hosted web page by Google. If Google
 * decides at any time to change this output, the class will likely fail. Please
 * e-mail me if this is the case, so we can get it working again.
 *
 * @author Zvi Landsman <zvi@jobshuk.com>
 * @website http://israelwebdev.wordpress.com or http://jobshuk.com
 */

/*
Copyright (C) 2009 Zvi Landsman

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class gtalkStatus {
  /**
   * Should be private : skype status server URL
   *
   * @var string
   */
  var $url;
  /**
   * Account we want to check
   *
   * @var string
   */
  var $account;
  /**
   * Status. Should also be private
   *
   * @var bool
   */
  var $status;
  /**
   * Status Message. Should also be private
   *
   * @var bool
   */
  var $status_msg;

  /**
   * Constructor
   *
   * @param string $account
   * @return checkSkype
   */
  function gtalkStatus($account_hash) {
    $this->account = $account_hash;
    $this->url = 'http://www.google.com/talk/service/badge/Show?tk='.$this->account;//.'.num';
  }

  /**
   * Just grab status
   *
   */
  private function _getStatus() {
    if (!empty($this->status)) {
      return;
    }
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
	curl_setopt($ch, CURLOPT_URL, $this->url);
	$frame = curl_exec($ch);
	curl_close($ch);


    $m=array();
    if(preg_match('|img id=\"b\" src=\"/talk/service/resources/([\w]*)|',$frame,$m)){
      //print_r($m);
      $this->status = $m[1]=='online'?2:1;
    }else{
      throw new Exception('Misformed Google output for gtalkStatus.class.php');
    }
    $end=substr($frame,strpos($frame,'display:none'));
    $this->status_msg = trim(strip_tags(substr($end,21)));
    //echo $this->status_msg;
  }

  /**
   * Check if status == 2
   *
   * @return bool true if so
   */
  function isOnline() {
    $this->_getStatus();
    return ($this->status != 1 && $this->status != 0);
  }

  function getStatusMsg() {
    $this->_getStatus();
    return $this->status_msg;
  }

  function getBadge(){//designed for Standard badge
    return '<iframe src="http://www.google.com/talk/service/badge/Show?tk='.$this->account.'&amp;w=200&amp;h=60" allowtransparency="true" width="200" frameborder="0" height="60"></iframe>';
  }

}
?>