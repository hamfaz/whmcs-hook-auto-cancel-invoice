# WHMCS Hook - Auto Cancel Invoice

### Set Interval Invoice Cancel
$intervalDayCancel = 14;

### Hook on DailyCronJob
add_hook("DailyCronJob", 1, "cronAutoCancelInvoice");
