<?php

use Phalcon\Mvc\Controller;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Http\Response;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;


class PlanetController extends Controller
{
    public function indexAction()
    {
        $planets = Planet::find(array(
                    "order" => "name"
                    ))->filter(
            function ($planet) {
                if ($planet->solarSystem->galaxy->cluster->dele == 0 && $planet->solarSystem->galaxy->dele == 0 && $planet->solarSystem->dele == 0 && $planet->dele == 0) {
                    return $planet;
                }
            }
        );
        if (count($planets) != 0) 
        {
            $validation_page = new Validation();
            $validation_page->add('page', new Between(array(
               'minimum' => 1,
               'maximum' => ceil(count($planets)/5),
               'message' => '1'
            )));

            $page = 1;        
            $messages = $validation_page->validate($_GET);
            if (!count($messages))
            {
                $page = $this->request->get("page");
            }

            $paginator = new PaginatorArray(
                array(
                    "data"  => $planets,
                    "limit" => 5,
                    "page"  => $page
                )
            );

            $this->view->planets = $paginator->getPaginate();
        }
    }

    public function TypeAction()
    {
        $types = TypeOfPlanet::find();
        $this->view->types = $types;
    }

    public function DeleteAction()
    {
        if ($this->request->isPost() == true) {

            $name = $this->request->getPost("name");

            $conditions = "name = :name:";

            $parameters = array(
            "name" => $name);

            $planets = Planet::find(
                array(
                    $conditions,
                    "bind" => $parameters
                )
            );

            foreach ($planets as $planet) {
                $planet->dele = 1;
                $success = $planet->save();
            }
            $response = new \Phalcon\Http\Response();
            $response->redirect("index");
            $response->send();
        }
        else
        {
            // Получение экземпляра Response
            $response = new \Phalcon\Http\Response();

            // Установка кода статуса
            $response->setStatusCode(404, "Not Found");

            // Установка содержимого ответа
            $response->setContent("<h3>404</h3><p>Сожалеем, но страница не существует</p>");

            // Отправка ответа клиенту
            $response->send();

        }

        $this->view->disable();
    }

    public function UpdAction()
    {
        if ($this->request->isPost() == true) {

            $id = $this->request->getPost("id");

            $conditions = "id = :id:";

            $parameters = array(
            "id" => $id);
            $planet = Planet::findFirst(
                array(
                    $conditions,
                    "bind" => $parameters
                )
            );
            $this->view->planet = $planet; 

            $solar_systems = SolarSystem::find(array(
                        "order" => "name"
                        ))->filter(
                function ($solar_system) {
                    if ($solar_system->galaxy->cluster->dele == 0 && $solar_system->galaxy->dele == 0 && $solar_system->dele == 0) {
                        return $solar_system;
                    }
                }
            );
            $this->view->solar_systems = $solar_systems; 

            $types = TypeOfPlanet::find();  
            $this->view->types = $types;         

        }
        else
        {
            // Получение экземпляра Response
            $response = new \Phalcon\Http\Response();

            // Установка кода статуса
            $response->setStatusCode(404, "Not Found");

            // Установка содержимого ответа
            $response->setContent("<h3>404</h3><p>Сожалеем, но страница не существует</p>");

            // Отправка ответа клиенту
            $response->send();

        }
    }

    public function UpdaterAction()
    {
        if ($this->request->isPost() == true) {

            

            $validation = new Validation();

            $validation->add('name', new PresenceOf(array(
               'message' => 'Вы ввели пустое название<br>'
            )));
            $validation->add('name', new StringLength(array(
                'max' => 100,
                'min' => 1,
                'messageMaximum' => 'Вы ввели слишком большое название<br />',
                'messageMinimum' => 'Вы ввели слишком маленькое название<br />'
            )));
            $validation->add('name', new RegexValidator(array(
               'pattern' => '/[a-zA-Zа-яА-ЯЁё0-9]{1}[a-zA-Zа-яА-ЯЁё0-9\s]{0,99}/u',
               'message' => 'Введите название правильно<br />'
            )));

            $validation->add('weight', new StringLength(array(
                'max' => 6,
                'min' => 1,
                'messageMaximum' => 'Такая большая звезда быть не может<br />',
                'messageMinimum' => 'Такая маленькая звезда быть не может<br />'
            )));
            $validation->add('weight', new PresenceOf(array(
               'message' => 'Вы ввели пустой вес<br />'
            )));
            $validation->add('weight', new RegexValidator(array(
               'pattern' => '/[0-9]{1,9}/',
               'message' => 'Введите вес правильно<br />'
            )));

            $validation->add('id', new PresenceOf(array(
               'message' => 'Ой<br />'
            )));
            $validation->add('id', new StringLength(array(
                'max' => 9,
                'min' => 1,
                'messageMaximum' => 'ОЙ<br />',
                'messageMinimum' => 'ОЙ<br />'
            )));           
            
            $validation->add('solar_system', new PresenceOf(array(
               'message' => 'Вы ввели пустую солнечную систему<br />'
            )));
            $validation->add('type', new PresenceOf(array(
               'message' => 'Вы ввели пустой тип<br />'
            )));

            $messages = $validation->validate($_POST);
            if (!count($messages)) 
            {                 
                global $name;
                global $id;
                $name = $this->request->getPost("name");
                $weight = $this->request->getPost("weight");
                $id = $this->request->getPost("id");
                $solar_system = $this->request->getPost("solar_system");
                $type = $this->request->getPost("type");

                $planet = Planet::find(
                    array(
                        "order" => "name",
                    )
                )->filter(
                    function ($planet) {
                        global $name;
                        global $id;
                        if ($planet->SolarSystem->Galaxy->cluster->dele == 0 && $planet->SolarSystem->galaxy->dele == 0 && $planet->SolarSystem->dele == 0 && $planet->name == $name && $planet->id != $id ) {
                            return $planet;
                        }
                    }
                );
                if ($planet == false) 
                {
                    $conditions = "name = :name: AND dele = 0";

                    $parameters = array(
                    "name" => $solar_system);

                    $solar_system_id = SolarSystem::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    $conditions = "name = :name:";

                    $parameters = array(
                    "name" => $type);

                    $type_id = TypeOfPlanet::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    $conditions = "id = :id:";

                    $parameters = array(
                    "id" => $id);

                    $planet = Planet::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );
                    try 
                    {

                        $planet->name = $name;
                        $planet->weight = $weight;
                        $planet->type = $type_id->id;
                        $planet->solar_system = $solar_system_id->id;
                        $success = $planet->save();
                        if ($success) {
                            echo "Данные упешно изменены";
                        }
                        else
                            echo "Данные не изменены";
                    } 
                    catch (InvalidArgumentException $e) {
                        echo "Что-то пошло не так, пожалуйста проверьте корректность ввода";
                    }
                }
                else echo "Планета с таким названием уже есть";
            
            }
            else
                foreach ($messages as $message) {
                    echo $message;
                }
        }
        else
            echo "Опаньки, поста то нет";

        $this->view->disable();
    }

    public function InsertAction() 
    {
        $solar_systems = SolarSystem::find(array(
                      "order" => "name"
                    ))->filter(
            function ($solar_system) {
                if ($solar_system->galaxy->cluster->dele == 0 && $solar_system->galaxy->dele == 0 && $solar_system->dele == 0) {
                    return $solar_system;
                }
            }
        );
        $this->view->solar_systems = $solar_systems; 
        $types = TypeOfPlanet::find();  
        $this->view->types = $types;
    }

    public function EnterAction()
    {
        if ($this->request->isPost() == true) {

            

            $validation = new Validation();
            $validation->add('name', new PresenceOf(array(
               'message' => 'Вы ввели пустое название<br>'
            )));
            $validation->add('name', new StringLength(array(
                'max' => 100,
                'min' => 1,
                'messageMaximum' => 'Вы ввели слишком большое название<br />',
                'messageMinimum' => 'Вы ввели слишком маленькое название<br />'
            )));
            $validation->add('name', new RegexValidator(array(
               'pattern' => '/[a-zA-Zа-яА-ЯЁё0-9]{1}[a-zA-Zа-яА-ЯЁё0-9\s]{0,99}/u',
               'message' => 'Введите название правильно<br />'
            )));

            $validation->add('weight', new StringLength(array(
                'max' => 6,
                'min' => 1,
                'messageMaximum' => 'Такая большая чёрная дыра быть не может<br />',
                'messageMinimum' => 'Такая маленькая чёрная дыра быть не может<br />'
            )));
            $validation->add('weight', new PresenceOf(array(
               'message' => 'Вы ввели пустой вес<br />'
            )));
            $validation->add('weight', new RegexValidator(array(
               'pattern' => '/[0-9]{1,9}/',
               'message' => 'Введите вес правильно<br />'
            )));            
            
            $validation->add('solar_system', new PresenceOf(array(
               'message' => 'Вы ввели пустую солнечную систему<br />'
            )));
            $validation->add('type', new PresenceOf(array(
               'message' => 'Вы ввели пустой тип<br />'
            )));

            $messages = $validation->validate($_POST);
            if (!count($messages)) 
            {                 
                global $name;
                $name = $this->request->getPost("name");
                $weight = $this->request->getPost("weight");
                $id = $this->request->getPost("id");
                $solar_system = $this->request->getPost("solar_system");
                $type = $this->request->getPost("type");

                $planet = Planet::find(
                    array(
                        "order" => "name",
                    )
                )->filter(
                    function ($planet) {
                        global $name;
                        if ($planet->SolarSystem->Galaxy->cluster->dele == 0 && $planet->SolarSystem->galaxy->dele == 0 && $planet->SolarSystem->dele == 0 && $planet->name == $name ) {
                            return $planet;
                        }
                    }
                );
                if ($planet == false) 
                {
                    $conditions = "name = :name: AND dele = 0";

                    $parameters = array(
                    "name" => $solar_system);

                    $solar_system_id = SolarSystem::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    $conditions = "name = :name:";

                    $parameters = array(
                    "name" => $type);

                    $type_id = TypeOfPlanet::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    try 
                    {
                        $planet = new Planet();
                        $planet->name = $name;
                        $planet->weight = $weight;
                        $planet->type = $type_id->id;
                        $planet->solar_system = $solar_system_id->id;
                        $success = $planet->save();
                        if ($success) {
                            echo "Планета добавлена";
                        }
                        else
                            echo "Планета не добавлена";
                    } 
                    catch (InvalidArgumentException $e) {
                        echo "Что-то пошло не так, пожалуйста проверьте корректность ввода";
                    }
                }
                else echo "Планета с таким названием уже есть";
            
            }
            else
                foreach ($messages as $message) {
                    echo $message;
                }
        }
        else
            echo "Опаньки, поста то нет";

        $this->view->disable();
    }
}


?>