<?php
# Ratings extension for MediaWiki v1.1 Sanford Poon 2007
# Based on Poll extension v1.4 Eric David / 2006
# http://dev.openbig.net/index.php/Dev.openbig.net:Ratings
# <Rating>
# Question
# Answer 1 (Value 1)
# Answer 2
# ...
# Answer n (Value n)
# </Rating>
#
# To activate the extension, include it from your LocalSettings.php
# with: include("extensions/Ratings/Ratings.php");
if (!defined('MEDIAWIKI')) die();
$wgRatingsMaxTextLengthPerLine = 36;
$wgRatingsMaxNameLength = 36;
$wgRatingsMaxPollItemLength = 24;
$wgRatingsMaxPollTitleLength = 240;
$wgRatingsMaxMessageLength = 480;
$wgRatingsUseCaptcha = true;
$wgExtensionFunctions[] = "efRatings";
$wgSpecialPages['RatingsAdminPanel'] = 'RatingsAdminPanel';
$wgExtensionCredits['parserhook'][] = array('author' => 'Sanford Poon', 'description' => 'Adds rating and discussion capability to MediaWiki.', 'name' => 'Ratings (1.1.2)', 'url' => 'http://dev.openbig.net/index.php/Dev.openbig.net:Ratings');
function efRatings() {
    global $wgParser, $IP;
    require_once ("$IP/includes/SpecialPage.php");
    // *******************************************************************************************
    // Generic Ratings Handler class for storing settings and handy functions
    // *******************************************************************************************
    class RatingHandler {
        var $vote_table = "ratings_vote";
        var $msg_table = "ratings_message";
        var $info_table = "ratings_info";
        function RatingHandler() {
        }
        function text($str) {
            global $wgUser, $IP;
            /* we may add some other languages here as well */
            $lang = $wgUser->getOption('language');
            if (is_file("$IP/extensions/Ratings/Ratings.i18n-$lang.php")) {
                require ("Ratings.i18n-$lang.php");
            } else {
                require ("Ratings.i18n-en.php");
            }
            if (!isset($msg[$str])) {
                return "error @ text($str)";
            }
            return $msg[$str];
        }
        function isValidMessage($name, $message, $needcap) {
            global $recaptcha_private_key, $wgRatingsUseCaptcha, $wgRatingsMaxMessageLength;
            if (!$wgRatingsUseCaptcha) return "";
            if (trim(strip_tags($name)) == "") return $this->text("empty_name");
            if (trim(strip_tags($message)) == "") return $this->text("empty_message");
            if ($needcap) {
                $recaptcha_response = recaptcha_check_answer($recaptcha_private_key, wfGetIP(), $_POST['recaptcha_challenge_field'], $_POST['captcha_response']);
                if (!$recaptcha_response->is_valid) {
                    return $this->text("wrong_captcha");
                }
            }
            if (strlen($message) > $wgRatingsMaxMessageLength) return $this->text("message_too_long1") . $wgRatingsMaxMessageLength . $this->text("message_too_long2");
            return "";
        }
        function trimName($name) {
            global $wgRatingsMaxNameLength;
            return strip_tags(substr(trim($name), 0, $wgRatingsMaxNameLength));
        }
        function trimText($message) {
            global $wgRatingsMaxTextLengthPerLine;
            return preg_replace('/([\x21-\x7e]{' . $wgRatingsMaxTextLengthPerLine . ',' . $wgRatingsMaxTextLengthPerLine . '})([\x21-\x7e])/', "$1\n$2", $message);
        }
        function pollItemHandling($item) {
            global $wgRatingsMaxPollItemLength;
            return strip_tags(substr(trim($item), 0, $wgRatingsMaxPollItemLength));
        }
        function pollTitleHandling($title) {
            global $wgRatingsMaxPollTitleLength, $wgRatingsMaxTextLengthPerLine;
            return nl2br(preg_replace('/([\s\x21-\x7e]{' . $wgRatingsMaxTextLengthPerLine . ',' . $wgRatingsMaxTextLengthPerLine . '})([\s\x21-\x7e])/', "$1\n$2", (strip_tags(substr(trim($title), 0, $wgRatingsMaxPollTitleLength)))));
        }
    }
    # Ratings Backend Special Pages
    class RatingsAdminPanel extends SpecialPage {
        function RatingsAdminPanel() {
            SpecialPage::SpecialPage("RatingsAdminPanel", 'RatingsAdminPanel', true, true, 'default', false);
        }
        function execute($par) {
            global $wgRequest, $wgOut, $wgScriptPath;
            $wgOut->addLink(array('rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'screen,projection', 'href' => "$wgScriptPath/extensions/Ratings/specialpage.css"));
            if (!$this->including()) {
                $wgOut->setPagetitle("Ratings Administration Panel");
                $wgOut->setArticleRelated(false);
            }
            $output = '<table class="ratingsadmin">
<tr><th>poll_user</th>
<th>poll_id</th>
<th>poll_msg</th>
<th>poll_date</th>
<th>edit</th>
<th>delete</th></tr>';
            if (!isset($_REQUEST['page'])) $page = 0;
            else $page = ($_REQUEST['page']-1) *10;
            $rating_handle = new RatingHandler();
            $dbr = &wfGetDB(DB_SLAVE);
            $tb_msg = $dbr->tableName($rating_handle->msg_table);
            $sql = "SELECT `poll_user`,`poll_msg` ,`poll_date` ,`poll_id` FROM $tb_msg \n" . " ORDER BY `poll_date` DESC LIMIT " . $page . ",10\n";
            $res = $dbr->query($sql, 'efRatings (class RatingsAdminPanel)');
            $i = 0;
            while ($comrow = $dbr->fetchObject($res)) {
                $output.= '<tr>
<td>' . $comrow->poll_user . '</td>
<td>' . $comrow->poll_id . '</td>
<td>' . $comrow->poll_msg . '</td>
<td>' . $comrow->poll_date . '</td>
<td>edit</td>
<td>delete</td>';
            }
            $dbr->freeResult($res);
            $msgtotal = $i;
            $output.= '</table>';
            $wgOut->addHTML($output);
        }
    }
    $wgParser->setHook("Rating", "efRenderRating");
    $wgParser->disableCache();
}
// *******************************************************************************************
// The callback function for converting the input text to HTML output
// $argv is an array containing any arguments passed to the extension like <example argument="foo" bar>..
// *******************************************************************************************
function efRenderRating($input, $argv = array()) {
    global $IP, $wgParser, $wgUser, $wgScriptPath, $wgTitle, $wgRequest, $wgArticlePath, $recaptcha_public_key, $wgOut, $wgRatingsUseCaptcha;
    if ($wgRatingsUseCaptcha) {
        require_once ("$IP/extensions/recaptcha/recaptchalib.php");
        $captcha_link = true;
        foreach($wgOut->mLinktags as $link) {
            if (strpos($link['href'], "/Ratings/captcha.css") !== false) {
                $captcha_link = false;
                break;
            }
        }
        if ($captcha_link) $wgOut->addLink(array('rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'screen,projection', 'href' => "$wgScriptPath/extensions/Ratings/captcha.css"));
    }
    $input = strip_tags($input);
    $lang = $wgUser->getOption('language');
    $rating_handle = new RatingHandler();
    $title = $wgTitle->getText();
    $nspace = $wgTitle->getNameSpace();
    $domain = empty($argv['domain']) ? "" : $argv['domain'];
    $small = empty($argv['small']) ? "" : $argv['small'];
    $locked = empty($argv['locked']) ? "" : $argv['locked'];
    $rating = empty($argv['normal']) ? "y" : "";
    $dbr = &wfGetDB(DB_SLAVE);
    $tb_vote = $dbr->tableName($rating_handle->vote_table);
    $tb_msg = $dbr->tableName($rating_handle->msg_table);
    $tb_info = $dbr->tableName($rating_handle->info_table);
    if ($small) {
        $bginfo = ' BGCOLOR=#F0F0F0';
        $bginfo2 = ' BGCOLOR=#FFFFFF';
    } else $bginfo = $bginfo2 = ' BGCOLOR=#F6F6F6';
    if ($locked) {
        $bginfo = ' BGCOLOR=#D0D0D0';
        $bginfo2 = ' BGCOLOR=#C0C0C0';
        $small = '1';
    }
    $vote_parm = empty($argv['vote']) || $argv['vote'] != "false" ? true : false;
    $comment_parm = empty($argv['comment']) || $argv['comment'] != "false" ? true : false;
    if (!$vote_parm && !$comment_parm) {
        $vote_parm = true;
        $comment_parm = true;
    }
    $wgParser->disableCache();
    $IPp = wfGetIP();
    if ($wgUser->mName == "") $user = $IPp;
    else $user = $wgUser->mName;
    if ($user == $IPp) $user2 = "";
    else $user2 = $user;
    $ID = strtoupper(md5($title . $input)); // ID of the poll
    $err = "";
    $errorValidation = "";
    if ($wgRequest->getVal('oldid') != "") $nspace = "Oldies";
    $lines = split("\n", $input);
    // *******************************************************************************************
    // SPECIAL OPTIONS : STATS, etc
    // *******************************************************************************************
    if ($lines[1] == "STATS") // special param : stats
    {
        $col0 = "COUNT(*)";
        $col1 = "COUNT(DISTINCT poll_id)";
        $col2 = "COUNT(DISTINCT poll_user)";
        $col3 = "timediff(now(),MAX(poll_date))";
        $col4 = "count(*)";
        $sql = "SELECT $col0, $col1, $col2, $col3 FROM $tb_vote";
        $res = $dbr->query($sql, 'efRenderRating (STATS)');
        $tab = $dbr->fetchObject($res);
        $clock = split(':', $tab->$col3);
        if ($clock[0] == '00' && $clock[1] == '00') {
            $x = $clock[2]+0;
            $y = $rating_handle->text("second");
        } else if ($clock[0] == '00') {
            $x = $clock[1]+0;
            $y = $rating_handle->text("minute");
        } else {
            $hr = ($clock[0]+0);
            if ($hr < 24) {
                $x = $hr;
                $y = $rating_handle->text("hour");
            } else {
                $x = floor($hr/24);
                $y = $rating_handle->text("day");
            }
        }
        $dbr->freeResult($res);
        $clockago = $x . $y . ($x > 1 ? '' : '');
        $sql = "SELECT $col4 FROM $tb_vote WHERE DATE_SUB(CURDATE(), INTERVAL 2 DAY) <= poll_date";
        $res = $dbr->query($sql, 'efRenderRating (STATS)');
        $tab2 = $dbr->fetchObject($res);
        $dbr->freeResult($res);
        return $rating_handle->text("stat_msg1") . $tab->$col1 . $rating_handle->text("stat_msg2") . $tab->$col2 . $rating_handle->text("stat_msg3") . $tab->$col2 . $rating_handle->text("stat_msg4") . $clockago . $rating_handle->text("stat_msg5") . $tab2->$col4 . $rating_handle->text("stat_msg6") . $err;
    }
    // *******************************************************************************************
    // REGULAR 'POST' VARIABLES TREATMENTS
    // *******************************************************************************************
    if (!empty($_POST['poll_ID'])) // POST Variables treatments
    {
        $_POST['poll_ID'] = trim($_POST['poll_ID'], "/");
        if (!empty($_POST['answer'])) {
            $_POST['answer'] = trim($_POST['answer'], "/");
            if ($_POST['poll_ID'] == $ID) // PROCESS THE VOTE
            {
                $sql = "DELETE FROM $tb_vote WHERE `poll_id` = '" . $ID . "' and `poll_ip` = '" . $IPp . "'";
                $dbr->query($sql, 'efRenderRating (PROCESS THE VOTE)');
                $sql = "DELETE FROM $tb_vote WHERE `poll_id` = '" . $ID . "' and `poll_user` = '" . mysql_real_escape_string($user) . "'";
                $dbr->query($sql, 'efRenderRating (PROCESS THE VOTE)');
                $sql = "INSERT INTO $tb_vote (`poll_id`, `poll_user`, `poll_ip`, `poll_answer`, `poll_date`)\n" . "\tVALUES ('" . $ID . "', '" . mysql_real_escape_string($user) . "', '" . wfGetIP() . "', '" . $_POST['answer'] . "', '" . date("Y-m-d H:i:s") . "')";
                $dbr->query($sql, 'efRenderRating (PROCESS THE VOTE)');
            }
        }
        if ($_POST['poll_ID'] == $ID && isset($_POST['newmessage'])) // PROCESS THE MESSAGE
        {
            $_POST['message'] = nl2br(strip_tags(trim($_POST['message'], "/")));
            $_POST['name'] = $rating_handle->trimName($_POST['name']);
            $_POST['message'] = $rating_handle->trimText($_POST['message']);
            if (ucfirst($_POST['name']) == $user2) {
                $_POST['name']="{<$user2>}";
            }
            $sql = "INSERT INTO $tb_msg " . "(`poll_id`, `poll_user`, `poll_ip`, `poll_msg`, `poll_date`)\n" . "\tVALUES ('" . $ID . "', '" . mysql_real_escape_string($_POST['name']) . "', '" . wfGetIP() . "', '" . mysql_real_escape_string($_POST['message']) . "', '" . date("Y-m-d H:i:s") . "')";
            $errorValidation = $rating_handle->isValidMessage($_POST['name'], $_POST['message'], $user2 == "");
            if ($errorValidation == "") $dbr->query($sql, 'efRenderRating (PROCESS THE MESSAGE)');
        }
    }
    // *******************************************************************************************
    // GETTING THE VOTES (some SQL requests)
    // *******************************************************************************************
    $col1 = "COUNT(*)";
    $sql = "SELECT `poll_answer`, $col1" . "\tFROM $tb_vote\n" . "\tWHERE " . "`poll_id` = '" . $ID . "'" . "\n" . "\tGROUP BY `poll_answer`";
    $res = $dbr->query($sql, 'efRenderRating (GETTING THE VOTE)');
    while ($row = $dbr->fetchObject($res)) $poll_result[$row->poll_answer] = $row->$col1;
    $dbr->freeResult($res);
    if (empty($poll_result)) $total = 0;
    else $total = array_sum($poll_result);
    // has the user already voted ?
    $sql = "SELECT `poll_answer` FROM $tb_vote\n" . "\tWHERE `poll_id` = '" . $ID . "' AND `poll_user` = '" . mysql_real_escape_string($user) . "'\n";
    $res = $dbr->query($sql, 'efRenderRating (GETTING THE VOTE)');
    $row = $dbr->fetchObject($res);
    $dbr->freeResult($res);
    // Getting the messages
    if (!isset($_REQUEST['page'])) $page = 0;
    else $page = ($_REQUEST['page']-1) *10;
    $sql = "SELECT `poll_user`,`poll_msg` ,`poll_date` FROM $tb_msg\n" . "\tWHERE " . "`poll_id` = '" . $ID . "' ORDER BY `poll_date` DESC LIMIT " . mysql_real_escape_string($page) . ",10\n";
    $res = $dbr->query($sql, 'efRenderRating (GETTING THE VOTE)');
    $i = 0;
    while ($comrow = $dbr->fetchObject($res)) {
        $compoll_result[$i++] = $comrow->poll_msg;
        $compoll_result[$i-1] = preg_replace("/^\s+/m", "", $compoll_result[$i-1]);
        $compoll_user[$i-1] = $comrow->poll_user;
        if (preg_match("/^{<.*>}$/",$compoll_user[$i-1])) {
                $user3 = substr($compoll_user[$i-1],2,strlen($compoll_user[$i-1])-4);
                $lparse = clone $wgParser;
                $output = $lparse->parse( "[[User:$user3|$user3]]", $wgParser->mTitle, $wgParser->mOptions, false, false );
                $compoll_user[$i-1] = $output->getText();
        }
        $compoll_date[$i-1] = $comrow->poll_date;
        if ($domain == 'DO NOT REGISTER' && $comrow[1] == 'DEL' && $comrow[0] == 'WikiSysop') return '<a name="' . $ID . '" id="' . $ID . '"></a>';
    }
    $dbr->freeResult($res);
    $msgtotal = $i;
    $col0 = "count(*)";
    $sql = "SELECT $col0 FROM $tb_msg WHERE `poll_id` = '" . $ID . "'";
    $msgcount = $dbr->query($sql, 'efRenderRating (GETTING THE VOTE)');
    $err = $err . mysql_error();
    $pageresult = $dbr->fetchObject($msgcount);
    $pagecount = intval($pageresult->$col0/10);
    $curpage = intval($page/10) +1;
    if (($pageresult->$col0%10) > 0) $pagecount++;
    $scripturi = "";
    if (array_key_exists("SCRIPT_URI", $_SERVER)) $scripturi = $_SERVER['SCRIPT_URI'];
    $currentpath = $scripturi . preg_replace("@&page=[0-9]+@", "", preg_replace("@/@", "?title=", preg_replace("@\?title=[^&]+&@", "&", preg_replace("@/index.php@", "", $_SERVER["REQUEST_URI"])))) . "&page=";
    $pagesnav = "<small>( " . $pageresult->$col0 . $rating_handle->text("nav_msg1") . $curpage . $rating_handle->text("nav_msg2") . $pagecount . $rating_handle->text("nav_msg3") . ")</small><br>";
    if ($curpage != 1 && $pagecount > 1) {
        $pagesnav.= "<a href=\"" . $currentpath . "1\">" . $rating_handle->text("first_page") . "</a> ";
    }
    if ($curpage == 1 && $pagecount > 1) {
        $pagesnav.= "<a href=\"" . $currentpath . "2\">" . $rating_handle->text("next_page") . "</a>";
    } else if ($pagecount == $curpage && $curpage != 1) {
        $pagesnav.= "<a href=\"" . $currentpath . ($_REQUEST['page']-1) . "\">" . $rating_handle->text("prev_page") . "</a> ";
    } else if ($pagecount > $curpage) {
        $pagesnav.= "<a href=\"" . $currentpath . ($_REQUEST['page']-1) . "\">" . $rating_handle->text("prev_page") . "</a> <a href=\"" . $currentpath . ($_REQUEST['page']+1) . "\">" . $rating_handle->text("next_page") . "</a>";
    }
    if ($curpage != $pagecount && $pagecount > 1) {
        $pagesnav.= " <a href=\"" . $currentpath . ($pagecount) . "\">" . $rating_handle->text("last_page") . "</a>";
    }
    $dbr->freeResult($msgcount);
    if ($lines[1] == "PURIFY") // Purify the poll_info table
    {
        $sql = "insert into $tb_msg (poll_id,poll_user,poll_msg) 
				select poll_info.poll_id,' AUTO ','DEL'
				from $tb_info,
				(select poll_title,min(poll_date),max(poll_date) poll_datemaxi
				from $tb_info
				group by poll_title) title
				where $tb_info.poll_title = title.poll_title
				and TIME_TO_SEC(title.poll_datemaxi)-TIME_TO_SEC(poll_info.poll_date)>2";
        $res = $dbr->query($sql, 'efRenderRating (PURIFY)');
        $dbr->freeResult($res);
        $sql = "UPDATE $tb_info SET poll_domain = \"DEL\" where poll_id in 
				(select msg.poll_id from $tb_msg msg
				where poll_msg = \"DEL\" and poll_user = \" AUTO \")";
        $res = $dbr->query($sql, 'efRenderRating (PURIFY)');
        $sql = "delete from $tb_info where poll_domain = \"DEL\"";
        $res = $dbr->query($sql, 'efRenderRating (PURIFY)');
        $sql = "delete from $tb_msg where poll_msg = \"DEL\" and poll_user = \" AUTO \"";
        $res = $dbr->query($sql, 'efRenderRating (PURIFY)');
        if ($lines[1] == "PURIFY") return "<!-- POLL PURIFY PROCESS -->";
    }
    // *******************************************************************************************
    // building HTML
    // *******************************************************************************************
    if ($rating != "") {
        $str = "";
        $rateform = "";
        $messageform = "";
        if ($nspace == '' && $domain != 'DO NOT REGISTER' && $locked == '') // register the poll in poll_info
        {
            $sql = "DELETE FROM $tb_info WHERE `poll_id` = '" . $ID . "'";
            $dbr->query($sql, 'efRenderRating (BUILDING HTML)');
            $sql = "INSERT INTO $tb_info " . "(`poll_id`, `poll_txt`, `poll_date`,`poll_title`,`poll_domain`)\n" . "\tVALUES ('" . $ID . "', '" . mysql_real_escape_string($input) . "', '" . date("Y-m-d H:i:s") . "', '" . mysql_real_escape_string($title) . "','" . mysql_real_escape_string($domain) . "')";
            $dbr->query($sql, 'efRenderRating (BUILDING HTML)');
            $str.= "<!-- regdom $domain -->";
        } else $str.= "<!-- no regdom -->";
        if ($errorValidation != "") {
            $str.= $rating_handle->text("error_msg1") . $errorValidation . $rating_handle->text("error_msg2");
        }
        if ($vote_parm) {
            $str.= '<table width="340" cellmargin=0 cellspacing=0><tr><td ' . $bginfo . '><a name="' . $ID . '" id="' . $ID . '"></a>' . '<b' . ($small == '' ? '' : ' style="line-height: 100%"') . '>' . $rating_handle->pollTitleHandling($lines[1]) . '</b><font size=1> (' . $rating_handle->text("vote_msg1") . $total . $rating_handle->text("vote_msg2") . ')</font>';
            $rateform.= '<form name="poll" method="POST" action="#">' . '<input type="hidden" name="poll_ID" value="' . ($ID) . '"><SELECT NAME="answer">  ';
            $nbansw = count($lines) -1;
            $totalvote = 0;
            $accurate = 0;
            $maxrate = $nbansw-2;
            for ($i = 2;$i < $nbansw;$i++) {
                if ($total > 0) {
                    if (empty($poll_result[$i])) $res = 0;
                    else $res = $poll_result[$i];
                    $totalvote+= $res;
                    $accurate+= $res*($i-1);
                }
                $rateform.= '<OPTION VALUE="' . $i . '">' . $rating_handle->pollItemHandling($lines[$i]) . "\n";
            }
            $finalrate = $totalvote == 0 ? 0 : floatval($accurate) /floatval($totalvote);
            $finalrate = sprintf("%01.2f", $finalrate) . "<span style=\"font-size: small\"> / $maxrate</span></small>";
            $finalrate = $totalvote == 0 ? $rating_handle->text("no_vote") : $finalrate;
            $rateform.= "</select> " . ' <input type=submit value="' . $rating_handle->text("vote_submit") . '"></form>';
            $str.= ' <div style="color:red; font-size:180% ">' . $finalrate . '<br/></div>';
            $str.= $locked != '' ? '' : $rateform;
            $str.= '</td></tr></table>';
        }
        $recaptchaHTML = '<tr><td colspan="4" ' . $bginfo . '><div id="captcha" class="captcha_registration captcha"><fieldset><legend>' . $rating_handle->text("recaptcha_fieldset") . '</legend><div class="captcha_challenge"><div id="recaptcha_div"></div><div class="captcha_input"><label>' . $rating_handle->text("recaptcha_input") . '</label><input type="text" name="captcha_response" id="captcha_response"/></div></div></fieldset></div></td></tr>';
        $recaptchaJScreate = 'Recaptcha.create("' . $recaptcha_public_key . '","recaptcha_div", {   theme: "white",   tabindex: 0,   callback: Recaptcha.focus_response_field, lang:  \'' . $lang . '\'});';
        $recaptchaJSdestroy = 'Recaptcha.destroy();';
        if ($comment_parm) {
            if (!$wgRatingsUseCaptcha || $user2 != "") {
                $recaptchaHTML = "";
                $recaptchaJScreate = "";
                $recaptchaJSdestroy = "";
            }
            $str.= '<div id="messageLink'.$ID.'"><a href="javascript:showMessageForm(\''.$ID.'\')">' . $rating_handle->text("write_message") . '</a></div>';
            $messageform = '<HR><form action="'. $wgTitle->getFullURL() . '" method="post"><input type="hidden" name="newmessage" value="true"><input type="hidden" name="action" value="purge" /><input type="hidden" name="poll_ID" value="%ID%"><table cellpadding="3" cellspacing="0" border="0" ' . $bginfo . '><tr ' . $bginfo . '><td align="right" valign="top" ' . $bginfo . '>' . $rating_handle->text("name") . ' </td><td valign="top" ' . $bginfo . '><input type="text" name="name" id="name" value="' . $user2 . '" size="30" class="comment_name_input" /> </td><td ' . $bginfo . '></td><td ' . $bginfo . '></td></tr><tr ' . $bginfo . '><td valign="top" align="right" ' . $bginfo . '>' . $rating_handle->text("comment") . '<br/><small>' . $rating_handle->text("nohtml") . '</small></td><td valign="top" colspan="2" ' . $bginfo . '><textarea class="txpCommentInputMessage" name="message" id="message"  cols="30" rows="5"></textarea> </td><td ' . $bginfo . '></td></tr>' . $recaptchaHTML . '</table><input type=submit value="' . $rating_handle->text("submit_comment") . '">' . "" . ' </form><font size=1><a href="javascript:hideMessageForm(\\\'%ID%\\\')">(' . $rating_handle->text("hide") . ')</a></font><HR>';
            $str.= '<table width="340" cellmargin=0 cellspacing=0><tr><td ' . $bginfo . '><div id="messageForm'.$ID.'"></div></td></tr></table>';
            if ($msgtotal > 0) {
                $str.= '<b' . ($small == '' ? '' : ' style="line-height: 100%"') . '>' . $rating_handle->text("user_comments") . '</b> ' . $pagesnav;
                for ($i = 0;$i < $msgtotal;$i++) { // list of message
                    $str.= '<table width="340" cellmargin=0 cellspacing=0><tr><td ' . $bginfo . '><div style="background: #f7f2cc; border: 1px solid #915858;margin: 5px;padding: 5px;"><p><b>' . $compoll_user[$i] . ':<br/></b> ' . $compoll_result[$i] . '</p><p align="right"><small>' . $compoll_date[$i] . ' </small></p></div></td></tr></table>';
                }
            }
        }
        if ($err != NULL) return '<B>' . $rating_handle->text("error") . ':</B><BR>' . $err;
        $recaptcha_js = "recaptcha_ajax.i18n.js";
        return '<link rel="stylesheet" href="'.$wgScriptPath.'/extensions/Ratings/captcha.css" type="text/css" /><script type="text/javascript"  src="'.$wgScriptPath.'/extensions/Ratings/' . $recaptcha_js . '"></script><script  type= "text/javascript">function showMessageForm(id) {var str=\''.$messageform.'\';document.getElementById(\'messageLink\'+id).innerHTML = ""; document.getElementById(\'messageForm\'+id).innerHTML = str.replace(/%ID%/g,id);' . $recaptchaJScreate . '}function hideMessageForm(id) {document.getElementById(\'messageForm\'+id).innerHTML = "";document.getElementById(\'messageLink\'+id).innerHTML = \'<a href="javascript:showMessageForm(\\\'\'+id+\'\\\')">' . $rating_handle->text("write_message") . '</a>\'; ' . $recaptchaJSdestroy . '}</script><BR><table class="floatright" style="border:1px outset" cellspacing=0 cellpadding=5><tr><td' . $bginfo . '>' . $str . '</td></tr></table>';
    }
}
?>
