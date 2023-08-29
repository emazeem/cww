<?php

class Role
{
    const Manager = 'manager';
    const Technician = 'technician';
    const Customer = 'customer';
}
class TaskStatus
{
    const Pending = 0;
    const Complete = 1;
}
class OrderStatus
{
    const Pending = 0;
    const Complete = 1;
}
class OrderPayment
{
    const Pending = 0;
    const Complete = 1;
}


function getNext4Sundays() {
    $currentDate = new DateTime();
    $upcomingSundays = array();
    for ($i = 0; $i < 4; $i++) {
        while ($currentDate->format('N') != 7) {
            $currentDate->add(new DateInterval('P1D'));
        }
        $upcomingSundays[] = $currentDate->format('Y-m-d');
        $currentDate->add(new DateInterval('P1W'));
    }
    return $upcomingSundays;

}
