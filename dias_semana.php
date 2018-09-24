<?php
$week = date("W");
/* for($i=0; $i<7; $i++){
    echo date('d/n/Y', strtotime('01/01 +' . ($week - 1) . ' weeks first day +' . ($i - 1) . ' day')) . '<br />';
} */
$i = 5;
echo date('d/n/Y', strtotime('01/01 +' . ($week - 1) . ' weeks first day +' . ($i - 2) . ' day')) . '<br />';
