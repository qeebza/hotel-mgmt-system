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

        // =========================
        // STEP 1: EMAIL VALIDATION
        // =========================
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors_ .= Util::displayAlertV1("Please enter a valid email address.", "warning");
        }

        if (empty($currentPassword)) {
            $errors_ .= Util::displayAlertV1("Current password is required before updating account details.", "warning");
        }

        // =========================
        // STEP 2: GET USER FIRST
        // =========================
        $cHandler = new CustomerHandler();
        $existingCustomer = $cHandler->getCustomerObjByCid($_POST["cid"]);

        // =========================
        // STEP 3: VERIFY CURRENT PASSWORD (MUST BE FIRST)
        // =========================
        if (!password_verify($currentPassword, $existingCustomer->getPassword())) {
            echo Util::displayAlertV1("Wrong current password.", "warning");
            exit();
        }

        // =========================
        // STEP 4: NEW PASSWORD VALIDATION
        // =========================
        if (!empty($newPassword)) {

            if (empty($confirmPassword)) {
                $errors_ .= Util::displayAlertV1("Please confirm your new password.", "warning");
            }

            if ($newPassword !== $confirmPassword) {
                $errors_ .= Util::displayAlertV1("New password and confirmation password do not match.", "warning");
            }

            if (!Util::isStrongPassword($newPassword)) {
                $errors_ .= Util::displayAlertV1(
                    "New password must be at least 8 characters and include uppercase, lowercase, number, and special character.",
                    "warning"
                );
            }

            // =========================
            // ⭐ NEW FEATURE ADDED HERE
            // =========================
            if (password_verify($newPassword, $existingCustomer->getPassword())) {
                $errors_ .= Util::displayAlertV1(
                    "New password cannot be the same as your current password.",
                    "warning"
                );
            }
        }

        // =========================
        // STEP 5: SHOW ERRORS
        // =========================
        if (!empty($errors_)) {
            echo $errors_;
        } else {

            try {

                $c = new Customer();
                $c->setId(Util::sanitize_xss($_POST["cid"]));
                $c->setFullName(Util::sanitize_xss($_POST["fullName"]));
                $c->setPhone(Util::sanitize_xss($_POST["phone"]));
                $c->setEmail(Util::sanitize_xss($_POST["email"]));

                // =========================
                // STEP 6: PASSWORD UPDATE
                // =========================
                if (!empty($newPassword)) {
                    $c->setPassword($newPassword);
                } else {
                    $c->setPassword($existingCustomer->getPassword());
                }

                if (
                    isset($_SESSION["authenticated"]) &&
                    $_SESSION["authenticated"][1] == "false" &&
                    isset($_COOKIE['is_admin']) &&
                    $_COOKIE['is_admin'] == "false"
                ) {

                    $cHandler->updateCustomer($c);
                    echo Util::displayAlertV1($cHandler->getExecutionFeedback(), "success");
                }

                if (isset($_SESSION["username"])) {
                    $_SESSION["username"] = $cHandler->getUsername($_POST["email"]);
                }

                if (isset($_SESSION["phoneNumber"])) {
                    $_SESSION["phoneNumber"] = $_POST["phone"];
                }

            } catch (InvalidArgumentException $e) {
                echo Util::displayAlertV1($e->getMessage(), "warning");
            }
        }
    }

} else {
    echo "failed";
}