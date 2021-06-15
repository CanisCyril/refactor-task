<?php

session_start(['org_id', 'user_id']);

function getInput($field) {
    if (isset($_POST[$field])) {
        return $_POST[$field];
    } else {
        return null;
    }
}

function redirect($address) {
    $base = dirname($_SERVER['SCRIPT_NAME']);
    header('Location: ' . $base . DIRECTORY_SEPARATOR . $address . '.php');
}

use Scripts\Database\User;
use Scripts\Database\Activity;

require_once 'scripts/database/User.php';
require_once 'scripts/database/Activity.php';

$user = new User($orgID, $userID);
$activity = new Activity($orgID);

// Submit button was clicked
if (!empty($_POST)) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'name':
                $namePattern = '/^[\p{L}. ]+$/u';
                $names = trim(getInput('name'));

                if (!preg_match($namePattern, $names)) {
                    $error[] = "Your name must only contain characters, spaces or dots.";
                }

                $names = explode(' ', $names, 2);

                if (count($names) < 2 || empty($names)) {
                    $error[] = 'Please provide a first and last name.';
                }

                if (empty($error)) {
                    // returns true on success, false on failure
                    if ($user->updateNames($names)) {

                        $activity->insert($userID, Activity::USER_ACTION, "Updated Names");
                        unset($activity);

                        $success[] = "Your profile was successfully updated";
                    } else {
                        $error[] = "It was not possible to update your profile at present time.";
                    }
                }
                break;
            case 'email':
                $email = trim(getInput('email'));

                if (empty($email)) {
                    $error[] = 'Please provide a valid email!';
                } else {

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $error[] = 'Please provide a valid email!';
                    } else {
                        // returns email address if found, or false if not found
                        $exists = $user->checkEmailExistence($email);

                        if (!empty($exists)) {
                            $error[] = "Email already in use. Please enter a different email and try again.";
                        }
                    }
                }

                if (empty($error)) {
                    // returns true on success, false on failure
                    if ($user->updateEmail($email)) {
                        $activity->insert($userID, Activity::USER_ACTION, "Updated email address");
                        unset($activity);

                        $success[] = "Your profile was successfully updated";
                    } else {
                        $error[] = "It was not possible to update your profile at present time.";
                    }
                }
                break;

            case 'password':
                $pw1 = trim(getInput('password1'));
                $pw2 = trim(getInput('password2'));

                if ($pw1 !== $pw2) {
                    $error[] = "Provided passwords do not match.";
                }

                if (empty($error)) {
                    // returns true on success, false on failure
                    if ($user->updatePassword($pw1)) {

                        $activity->insert($userID, Activity::USER_ACTION, "Updated password");
                        unset($activity);

                        $success[] = "Your profile was successfully updated";
                    } else {
                        $error[] = "It was not possible to update your profile at present time.";
                    }
                }
                break;

            default:
                break;
        }
    }
}

// If something was updated, update the session and view vars
if (!empty($success)) {

    if (isset($_POST['action'])) {

        switch ($_POST['action']) {
            case 'name':
                $_SESSION['fName'] = $firstName = $names[0];
                $_SESSION['lName'] = $lastName = $names[1];
                $_SESSION['realName'] = $realName = ($firstName . " " . $lastName);
                $_SESSION['success'] = implode('<br>', $success);
                redirect('profile');
                break;

            case 'email':
                $_SESSION['email'] = $email;
                $_SESSION['success'] = implode('<br>', $success);
                redirect('profile');
                break;

            default:
                $_SESSION['success'] = implode('<br>', $success);
                redirect('index');
                break;
        }
    }
}

if (!empty($error)) {
    $_SESSION['error'] = implode('<br>', $error);
    redirect('profile');
}
