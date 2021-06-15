<?php

/**
 * This is a stub file for the refactoring exercise
 * You can modify it as you see fit but the methods
 * are purely to provide a base level of functionality.
 */
namespace Scripts\Database;

class User
{
    public function __construct($orgID, $userID)
    {
        // N/A
    }

    public function updateNames($names)
    {
        return true;
    }

    public function checkEmailExistence($email)
    {
        return false;
    }

    public function updateEmail($email)
    {
        return true;
    }

    public function updatePassword($password)
    {
        return true;
    }
}
