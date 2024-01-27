<?php
$msg = str_replace("<B>", "", $msg);
$msg = str_replace("</B>", "", $msg);
$msg = str_replace("<br>", "\n", $msg);
?>
{!! $msg !!}

{{ url('/email/unsubscribe?address='.$addy) }}