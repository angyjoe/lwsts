<?php
/*
 * Author: Sari Haj Hussein
 */
// Takes a unix timestamp, e.g. time(); and converts to an "ago" format.
function time_ago($time)
{
    $length = array("60", "60", "24", "7", "4.35", "12"); // Lenghts of the periods in the timeperiod following.
    $period = array("Second", "Minute", "Hour", "Day", "Week", "Month", "Year"); // Names of the periods.
    
    $now = time(); // unix timestamp.
    $diff = $now - $time; // Substract supplied timestamp.

    // Checks if the timedifference exceeds the time periods.
    for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
       $diff /= $length[$i];
    }

    // Rounding.
    $diff = round($diff);

    // Adds preceding s for correct tense. 
    if($diff != 1) {
       $period[$i].= "s";
    }

    return "$diff $period[$i] ago";
}
?>