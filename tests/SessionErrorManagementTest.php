<?php

use PHPUnit\Framework\TestCase;

class SessionErrorManagementTest extends TestCase
{
    public function testSessionIdIsRegeneratedAfterLogin()
    {
        $loginCode = file_get_contents(
            __DIR__ . '/../app/process_login.php'
        );

        $this->assertStringContainsString(
            'session_regenerate_id(true)',
            $loginCode
        );
    }

    public function testPasswordIsNotStoredInSession()
    {
        $loginCode = file_get_contents(
            __DIR__ . '/../app/process_login.php'
        );

        $this->assertStringNotContainsString(
            '$_SESSION["password"]',
            $loginCode
        );
    }

    public function testLogoutUsesPostRequest()
    {
        $logoutCode = file_get_contents(
            __DIR__ . '/../app/process_logout.php'
        );

        $this->assertStringContainsString(
            '$_SERVER["REQUEST_METHOD"] == "POST"',
            $logoutCode
        );
    }

    public function testDatabaseErrorIsHiddenFromUsers()
    {
        $databaseCode = file_get_contents(
            __DIR__ . '/../app/DB.php'
        );

        $this->assertStringContainsString(
            'error_log(',
            $databaseCode
        );
        $this->assertStringContainsString(
            'Server error occurred. Please try again later.',
            $databaseCode
        );
        $this->assertStringNotContainsString(
            'echo $error->getMessage()',
            $databaseCode
        );
    }
}
