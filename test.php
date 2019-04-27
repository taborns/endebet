<?php
 
$data = unserialize('a:9:{s:10:"no_of_days";i:34;s:17:"additional_guests";s:0:"";s:13:"check_in_date";s:10:"2019-04-27";s:14:"check_out_date";s:10:"2019-05-31";s:6:"guests";i:1;s:10:"listing_id";i:326;s:7:"upfront";d:328;s:7:"balance";d:6541.8;s:5:"total";d:6869.8;}');
$data['phone'] = '09234324';
print(serialize($data));
?>