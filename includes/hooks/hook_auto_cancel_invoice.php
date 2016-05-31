<?php
/**
 * WHMCS - Hook Auto Cancel Invoice
 *
 * @link https://github.com/hamfaz/whmcs-hook-auto-cancel-invoice.git
 * @author hamam fajar <hamamfajar@gmail.com> | @hamfaz
 * @version 0.5
 *
 */


if (!defined("WHMCS"))
	die("This file cannot be accessed directly");


/**
 * function cron auto cancel invoice
 *
 * @return response
 */
function cronAutoCancelInvoice()
{

	/**
	 * @var bool
	 */
  $data = array();

	/**
	 * interval day cancel from invoice date / invoice duedate
	 * @var int
	 */
	$intervalDayCancel = 14;

  /**
   * select invoice
   */
  $query = "SELECT * FROM tblinvoices i
  WHERE (
    i.`status`='Unpaid' AND i.duedate!='0000-00-00'  AND i.duedate+INTERVAL {$intervalDayCancel} DAY<=NOW()
  ) OR (
    i.`status`='Unpaid'  AND i.duedate='0000-00-00' AND i.date+INTERVAL {$intervalDayCancel} DAY<=NOW()
  ) ORDER BY i.date DESC";
  $result = mysql_query($query);

	/**
	 * while data result
	 */
  while($row = mysql_fetch_assoc($result))
  {
    $data['invoiceid'][] = $row['id'];
  }

  /**
   * save log to whmcs logActivity
	 * AdminArea Log View : Utilities -> Logs -> Activity Log
   */
  logActivity('Cron Auto Cancel Invoice - ' . json_encode($data));

  /**
   * update invoice to Cancelled
   */
  $query = "UPDATE tblinvoices i SET i.`status`='Cancelled'
  WHERE (
    i.`status`='Unpaid' AND i.duedate!='0000-00-00' AND i.duedate+INTERVAL {$intervalDayCancel} DAY<=NOW()
  ) OR (
    i.`status`='Unpaid'  AND i.duedate='0000-00-00' AND i.date+INTERVAL {$intervalDayCancel} DAY<=NOW()
  )";
  $result = mysql_query($query);

}


/**
 * add hook to DailyCronJob
 * Whmcs Docs : http://docs.whmcs.com/Hooks:DailyCronJob
 */
add_hook("DailyCronJob", 1, "cronAutoCancelInvoice");
