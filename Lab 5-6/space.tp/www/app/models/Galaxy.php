<?php

use Phalcon\Mvc\Model;

class Galaxy extends Model
{
    protected $id;
    protected $name;
    protected $dele;
    protected $size;
    protected $type_id;
    protected $cluster_id;

    public function initialize()
    {
        $this->belongsTo("cluster_id", "Cluster", "id");
        $this->belongsTo("type_id", "TypeOfGalaxy", "id");
        $this->hasMany("id", "BlackHole", "galaxy_id");
        $this->hasMany("id", "SolarSystem", "galaxy_id");


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

    public function setDele($i)
    {
        $this->dele = $this->dele + 1;
    }

    public function getDele()
    {
        return $this->dele;
    }

    public function setSize($size)
    {
        $size = htmlspecialchars(strip_tags(stripslashes(trim($size))));
        if ($size < 0) {
            throw new \InvalidArgumentException('Размер не может быть отрицательным');
        }
        $this->size = $size;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setType($type)
    {
        $this->type_id = $type;
    }

    public function getType_id()
    {
        return $this->type_id;
    }

    public function setCluster($cluster)
    {
        $this->cluster_id = $cluster;
    }

    public function getCluster_id()
    {
        return $this->cluster_id;
    }

}


?>