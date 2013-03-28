<?php
// Pre-requisites !!!!!
// *** Clickatell MO / 2 Way / Short Code / Longcode
// PHP ... and file_get_contents. Hack curl if you need to...
// Clickatell HTTP/S api
// Credits!

// This file needs to be the callback  URL for MO / 2 Way / Short Code / Longcode
// Set callback to GET or POST and add auth. use protection... always

//Problems? Send an email to repsycle@gmail.com

//I Wrote this little script for :: ZA CAPE TOWN RESISTANCE INGRESS - first LVL 8 in AFRICA

//check for required GET/POST/REQUEST
if(!empty($_REQUEST['from']) && !empty($_REQUEST['text']) && !empty($_REQUEST['api_id']))
{
    //fire up the class
    $groupmsg = new GroupMsg();

    //Extra security only allow from certain api_id
    //(you can also check if the callback is coming from the correct server/ip)
    if($_REQUEST['api_id'] == $groupmsg->api_id)
    {
        //Grab the vars from REQUEST
        $api_id        = $_REQUEST['api_id'];
        $source_mobile = $_REQUEST['from'];
        $text          = $_REQUEST['text'];

        // to avoid case issues, make it all lowercase
        $lower_text = strtolower($text);

        //break up the words using spaces
        $check_keyword = explode(' ',$lower_text);

        //Do we have the correct first keyword?
        if($check_keyword[0] == strtolower($groupmsg->keyword))
        {
            //Send the message
            $groupmsg->SendSMS($source_mobile,$text);
        }
    }
}

exit(9);

class GroupMsg
{
    //The clickatell url
    var $api_host = 'http://api.clickatell.com'; //use https if paranoid

    //set your clickatell details below
    var $api_username  = 'username'; //modify username
    var $api_password  = 'password'; //modify password
    var $api_id   	   = '1234567';  //modify api_id
    var $keyword       = 'KEYWORD';  //modify keyword
    var $shortcode     = '12345';    //modify MO number(incoming number and sender_id)
    var $group_name	   = 'My Group'; //modify group name

    function SendSMS($source_mobile,$text)
    {
        //The group that can send and receive sms
        //$group['Joe Soap']   = '111111111111';
        //$group['Fred Smith'] = '222222222222';
        //$group['Jane Doe']   = '333333333333';

        //Is this source mobile in the group?
        if(in_array($source_mobile,$group))
        {
            //search the array for the mobile
            if(($key = array_search($source_mobile, $group)) !== false)
            {
                //grap the sender
                $source_name=$key;

                //Remove mobile from group
                unset($group[$key]);
            }

            //build new group to send to
            $to = implode(',',$group);;

            //remove keyword and + from msg
            $msg = substr($text,strlen($this->keyword)+1);

            //Add group name and sender to front of msg
            $msg = $this->group_name.' from '.$source_name.' - '.$msg;

            //replace spaces with + in msg
            $msg = str_replace(' ','+',$msg);

            //build url string
            $url  = "$this->api_host/http/sendmsg?";
            $url .= "api_id=$this->api_id&user=$this->api_username&password=$this->api_password";
            $url .= "&to=$to&text=$msg&from=$this->shortcode&mo=1";

            //do the send
            file_get_contents($url);
        }
    }
}
?>