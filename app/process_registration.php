<?php

require '../lib/phpPasswordHashing/passwordLib.php';

require 'DB.php';
require 'Util.php';
require 'dao/CustomerDAO.php';
require 'models/Customer.php';
require 'handlers/CustomerHandler.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitBtn"])) {

    $errors_ = null;

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors_ .= Util::displayAlertV1("Please enter a valid email address.", "warning");
    }

    if (strlen($_POST["password"]) < 4 || strlen($_POST["password2"]) < 4) {
        $errors_ .= Util::displayAlertV1("A password of at least 4 characters is required", "warning");
    }

    if (!empty($_POST["password"]) && !empty($_POST["password2"])) {
        if ($_POST["password"] != $_POST["password2"]) {
            $errors_ .= Util::displayAlertV1("Password not match.", "warning");
        }
    }

    // ✅ NEW: PHONE VALIDATION (FRIENDLY VERSION)
    if (!preg_match('/^[0-9]{8,15}$/', $_POST["phoneNumber"])) {
        $errors_ .= Util::displayAlertV1("Invalid phone format. Please enter 8–15 digits only.", "warning");
    }

    if (!empty($errors_)) {
        echo $errors_;
    } else {

        $customer = new Customer();
        $customer->setFullName(Util::sanitize_xss($_POST["fullName"]));
        $customer->setEmail(Util::sanitize_xss($_POST["email"]));
        $customer->setPhone(Util::sanitize_xss($_POST["phoneNumber"]));
        $customer->setPassword(Util::sanitize_xss($_POST["password"]));

        $handler = new CustomerHandler();
        $handler->insertCustomer($customer);

        echo Util::displayAlertV1($handler->getExecutionFeedback(), "info");
    }
}