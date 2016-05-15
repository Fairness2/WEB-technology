<?php

use Phalcon\Mvc\Model;

class SolarSystem extends Model
{
    protected $id;
    protected $name;
    protected $dele;
    protected $galaxy_id;

    public function initialize()
    {
        $this->belongsTo("galaxy_id", "Galaxy", "id");
        $this->hasMany("id", "Planet", "solar_system_id");
        $this->hasMany("id", "Star", "solar_system_id");

        // Пропуск только при вставке
        $this->skipAttributesOnCreate(
            array(
                'dele',
                'id'
            )
        );

        // Пропуск только при обновлении
        $this->skipAttributesOnUpdate(
            array(
                'id'
            )
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $name = htmlspecialchars(strip_tags(stripslashes(trim($name))));
        // Имя слишком короткое?
        if (strlen($name) < 1) {
            throw new \InvalidArgumentException('Имя слишком короткое');
        }
        elseif (strlen($name) > 100) {
        	throw new \InvalidArgumentException('Имя слишком длинное');
        }
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDele()
    {
        return $this->dele;
    }

    public function setDele()
    {
        $this->dele = $this->dele + 1;
    }

    public function setGalaxy($galaxy)
    {
        $this->galaxy_id = $galaxy;
    }

    public function getGalaxy_id()
    {
        return $this->galaxy_id;
    }
}


?>