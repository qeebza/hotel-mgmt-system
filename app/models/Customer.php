<?php

class Customer
{
    private $cid;
    private $fullname;
    private $email;
    private $password;
    private $phone;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->cid;
    }

    public function setId($id)
    {
        $this->cid = $id;
    }

    public function getFullName()
    {
        return $this->fullname;
    }

    public function setFullName($fullName)
    {
        // Boundary Validation: Prevents database crash by catching overflow early
        if (strlen($fullName) > 50) {
            throw new InvalidArgumentException("Full name cannot exceed 50 characters.");
        }
        $this->fullname = $fullName;
    }
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $this->password = $hashed_password;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        // Data Type Validation: Enforces strict numeric structure via Regex
        if (!preg_match('/^[0-9]{8,15}$/', $phone)) {
            throw new InvalidArgumentException("Invalid phone format. Only numeric digits allowed.");
        }
        $this->phone = $phone;
    }
}
