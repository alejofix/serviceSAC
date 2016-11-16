<?php

define('IN_SCRIPT',1);
define('HESK_PATH','../');

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');
hesk_load_database_functions();

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

/* Check permissions for this feature */
hesk_checkPermission('can_view_tickets');
hesk_checkPermission('can_reply_tickets');

/* A security check */
hesk_token_check();

/* Ticket ID */
$trackingID = hesk_cleanID() or die($hesklang['int_error'].': '.$hesklang['no_trackID']);

/* Valid statuses */
$status_options = array(
	0 => $hesklang['open'],
	1 => $hesklang['wait_reply'],
	2 => $hesklang['replied'],
	3 => $hesklang['closed'],
	4 => $hesklang['in_progress'],
	5 => $hesklang['on_hold'],
);

/* New status */
$status = intval( hesk_REQUEST('s') );
if ( ! isset($status_options[$status]))
{
	hesk_process_messages($hesklang['instat'],'admin_ticket.php?track='.$trackingID.'&Refresh='.mt_rand(10000,99999),'NOTICE');
}

$locked = 0;

if ($status == 3) // Closed
{
	$action = $hesklang['ticket_been'] . ' ' . $hesklang['closed'];
    $revision = sprintf($hesklang['thist3'],hesk_date(),$_SESSION['name'].' ('.$_SESSION['user'].')');

    if ($hesk_settings['custopen'] != 1)
    {
    	$locked = 1;
    }

	// Notify customer of closed ticket?
	if ($hesk_settings['notify_closed'])
	{
		// Get ticket info
		$result = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` WHERE `trackid`='".hesk_dbEscape($trackingID)."' LIMIT 1");
		if (hesk_dbNumRows($result) != 1)
		{
			hesk_error($hesklang['ticket_not_found']);
		}
		$ticket = hesk_dbFetchAssoc($result);
		$ticket['dt'] = hesk_date($ticket['dt'], true);
		$ticket['lastchange'] = hesk_date($ticket['lastchange'], true);
		$ticket = hesk_ticketToPlain($ticket, 1, 0);

		// Notify customer
		require(HESK_PATH . 'inc/email_functions.inc.php');
		hesk_notifyCustomer('ticket_closed');
	}

	// Log who marked the ticket resolved
	$closedby_sql = ' , `closedat`=NOW(), `closedby`='.intval($_SESSION['id']).' ';
}
elseif ($status == 1 || $status == 2 || $status == 4 || $status == 5)
{
	$action = sprintf($hesklang['tsst'],$status_options[$status]);
    $revision = sprintf($hesklang['thist9'],hesk_date(),$status_options[$status],$_SESSION['name'].' ('.$_SESSION['user'].')');

	// Ticket is not resolved
	$closedby_sql = ' , `closedat`=NULL, `closedby`=NULL ';
}
else // Opened
{
	$action = $hesklang['ticket_been'] . ' ' . $hesklang['opened'];
    $revision = sprintf($hesklang['thist4'],hesk_date(),$_SESSION['name'].' ('.$_SESSION['user'].')');

	// Ticket is not resolved
	$closedby_sql = ' , `closedat`=NULL, `closedby`=NULL ';
}

hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` SET `status`='{$status}', `locked`='{$locked}' $closedby_sql , `history`=CONCAT(`history`,'".hesk_dbEscape($revision)."') WHERE `trackid`='".hesk_dbEscape($trackingID)."' LIMIT 1");

if (hesk_dbAffectedRows() != 1)
{
	hesk_error("$hesklang[int_error]: $hesklang[trackID_not_found].");
}

hesk_process_messages($action,'admin_ticket.php?track='.$trackingID.'&Refresh='.rand(10000,99999),'SUCCESS');
?>
