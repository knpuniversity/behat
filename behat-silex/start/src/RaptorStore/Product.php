<?php

namespace RaptorStore;

class Product
{
    public $id;

    public $name;

    public $author;

    public $description;

    public $price;

    public $isPublished = false;

    public $createdAt;

    public function __construct()
    {
        $this->createdAt = new \Datetime();
    }
}