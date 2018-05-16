## From Slack
```angular2html
$today = new DateTime();
$db = DB::get_conn();
$format = "%Y-%m-%d";
$start_field = $db->formattedDatetimeClause(
   '"Event"."Start"',
   $format
);
$end_field = $db->formattedDatetimeClause(
   '"Event"."End"',
    $format
);
$now = $today->format("Y-m-d");
$list = $list->where([
   $start_field . ' <= ?' => $now,
   $end_field . ' >= ?' => $now
]);
```
