<?php

ob_start();
session_start();


require '../lib/phpPasswordHashing/passwordLib.php';
require 'DB.php';
require 'Util.php';
require 'dao/CustomerDAO.php';
require 'models/Customer.php';
require 'handlers/CustomerHandler.php';

if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"][1] == "false") {

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitBtn"])) {
        $errors_ = null;

        $email = isset($_POST["email"]) ? Util::sanitize_xss($_POST["email"]) : "";
        $currentPassword = isset($_POST["currentPassword"]) ? $_POST["currentPassword"] : "";
        $newPassword = isset($_POST["newPassword"]) ? $_POST["newPassword"] : "";
        $confirmPassword = isset($_POST["confirmPassword"]) ? $_POST["confirmPassword"] : "";

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors_ .= Util::displayAlertV1("Please enter a valid email address.", "warning");
        }

        if (empty($currentPassword)) {
            $errors_ .= Util::displayAlertV1("Current password is required before updating account details.", "warning");
        }

        if (!empty($newPassword)) {
            if (empty($confirmPassword)) {
                $errors_ .= Util::displayAlertV1("Please confirm your new password.", "warning");
            }
            if ($newPassword !== $confirmPassword) {
                $errors_ .= Util::displayAlertV1("New password and confirmation password do not match.", "warning");
            }
            if (!Util::isStrongPassword($newPassword)) {
                $errors_ .= Util::displayAlertV1("New password must be at least 8 characters and include uppercase, 
                lowercase, number, and special character.", "warning");
            }
        }

        if (!empty($errors_)) {
            echo $errors_;
        } else {
            $cHandler = new CustomerHandler();

            if (!$cHandler->isCurrentPasswordValid($currentPassword, $email)) {
                echo Util::displayAlertV1("Current password is incorrect.", "warning");
                exit;
            }

            $c = new Customer();
            $c->setId(Util::sanitize_xss($_POST["cid"]));
            $c->setFullName(Util::sanitize_xss($_POST["fullName"]));
            $c->setPhone(Util::sanitize_xss($_POST["phone"]));
            $c->setEmail($email);

            if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"][1] == "false" &&
                isset($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == "false") {
                if (!empty($newPassword)) {
                    $c->setPassword(Util::sanitize_xss($newPassword));
                    $cHandler->updateCustomer($c);
                } else {
                    $cHandler->updateCustomerProfile($c);
                }

                echo Util::displayAlertV1($cHandler->getExecutionFeedback(), "success");    
            }

            if (isset($_SESSION["username"])) {
                $_SESSION["username"] = $cHandler->getUsername($email);
            }
            if (isset($_SESSION["phoneNumber"])) {
                $_SESSION["phoneNumber"] = $_POST["phone"];
            }
        }
    }

} else {
    echo "failed";
}

