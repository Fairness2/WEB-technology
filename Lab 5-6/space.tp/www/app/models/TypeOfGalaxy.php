<?php

use Phalcon\Mvc\Model;

class TypeOfGalaxy extends Model
{
   protected $id;
    protected $name;
    protected $description;

    public function initialize()
    {
        $this->hasMany("id", "Galaxy", "type_id");
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }
}


?>