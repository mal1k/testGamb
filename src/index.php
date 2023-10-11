<?php
declare(strict_types=1);

require 'classes/Environment.php';
require 'classes/Database.php';
require 'classes/User.php';

use classes\Database;
use classes\Environment;
use classes\User;

$env = new Environment();

// ~~~~~~~~~~~~~~~~~
// testing
// ~~~~~~~~~~~~~~~~~

// createUser();
// updateUser(); # set user ID as parameter
// getUserData(); # set user ID as parameter
// deleteUser(); # set user ID as parameter



// ~~~~~~~~~~~~~~~~~
// test functions 
// ~~~~~~~~~~~~~~~~~

# create user
function createUser() {
    $user = new User();
    $user->setBalance(500);
    $user->setBirthDate( date('Y-m-d', strtotime('-20 years')) );
    $user->setLastVisitDate( date('Y-m-d', strtotime('-1 day')) );
    $createdUserInfo = $user->save();

    echo '<pre>';
    print_r($createdUserInfo); # to view user data array
    echo '</pre>';
}

# update user
function updateUser($userID) {
    $user = new User($userID); # update user that we have created upper
    $user->setBalance($user->getBalance() + rand(100, 999)); # get current balance and increment random value from 100 till 999
    $user->setBirthDate( date('Y-m-d', strtotime('-20 years')) );
    $user->setLastVisitDate( date('Y-m-d', strtotime('-1 day')) );
    $user->save();

    echo 'New balance: ' . $user->getBalance(); # view updated balance
}
    
# get user data from DB
function getUserData($userID) {
    $user = new User($userID);
    $userData = $user->getData();

    echo '<pre>';
    print_r($userData); # to view user data array
    echo '</pre>';
}

# delete user
function deleteUser($userID) {
    $user = new User($userID);
    $user->delete();

    echo 'Successfully deleted';
}



// ~~~~~~~~~~~~~~~~~
# to create user table
// ~~~~~~~~~~~~~~~~~

createUserTable($env);
function createUserTable($env) {
    $db = new Database($env);
    $connection = $db->getConnection();

    $connection->query("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            balance FLOAT NOT NULL,
            lastVisitDate DATE,
            birthDate DATE
        );
    ");

    $db->close();
}