<?php
$users[] = array('name' => 'Sergio', 'age' => 22);
$users[] = array('name' => 'Carlos', 'age' => 29);
$users[] = array('name' => 'Iván', 'age' => 24);
$users[] = array('name' => 'Jorge', 'age' => 21);
$users[] = array('name' => 'Dani', 'age' => 25);
$users[] = array('name' => 'Jose', 'age' => 27);
$users[] = array('name' => 'David', 'age' => 19);
$users[] = array('name' => 'Esteban', 'age' => 35);

foreach ($users as $key => $row) {
    $aux[$key] = $row['age'];
}

array_multisort($aux, SORT_ASC, $users);

foreach ($users as $key => $row) {
    echo $row['name'].' '.$row['age'].'<br/>';
}

