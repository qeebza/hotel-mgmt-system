<?php

class Reservation extends Booking
{
    private $start;
    private $end;
    private $type;
    private $requirement;
    private $adults;
    private $children;
    private $requests;
    private $timestamp;
    private $hash;

    public function __construct()
    {
        parent::__construct();
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setStart($start)
    {
        // Convert the input string and today's date into comparable DateTime objects
        $start_obj = new DateTime($start);
        $today = new DateTime('today');
        
        // Business Logic Validation 1: Prevent booking in the past
        if ($start_obj < $today) {
            throw new LogicException("Chronological Error: Check-in date cannot be in the past.");
        }
        
        $this->start = $start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function setEnd($end)
    {
        // Ensure start is set first so we have something to compare against
        if (empty($this->start)) {
            throw new LogicException("System Error: Start date must be initialized before End date.");
        }

        $start_obj = new DateTime($this->start);
        $end_obj = new DateTime($end);
        
        // Business Logic Validation 2: Prevent negative pricing (Time Travel bug)
        if ($end_obj <= $start_obj) {
            throw new LogicException("Chronological Error: Check-out date must be strictly after the Check-in date.");
        }
        
        $this->end = $end;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getRequirement()
    {
        return $this->requirement;
    }

    public function setRequirement($requirement)
    {
        $this->requirement = $requirement;
    }

    public function getAdults()
    {
        return $this->adults;
    }

    public function setAdults($adults)
    {
        $this->adults = $adults;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function getRequests()
    {
        return $this->requests;
    }

    public function setRequests($requests)
    {
        $this->requests = $requests;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function requirement()
    {
        return array("No preference", "Non-smoking", "Smoking");
    }
}
