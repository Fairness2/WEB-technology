<?php

use Phalcon\Mvc\Controller;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Http\Response;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;


class SolarSystemController extends Controller
{
    public function indexAction()
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
        if (count($solar_systems) != 0) 
        {
            $validation_page = new Validation();
            $validation_page->add('page', new Between(array(
               'minimum' => 1,
               'maximum' => ceil(count($solar_systems)/5),
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
                    "data"  => $solar_systems,
                    "limit" => 5,
                    "page"  => $page
                )
            );

            $this->view->solar_systems = $paginator->getPaginate();
        }
    }

    public function DeleteAction()
    {
        if ($this->request->isPost() == true) {

            $name = $this->request->getPost("name");

            $conditions = "name = :name:";

            $parameters = array(
            "name" => $name);

            $solar_systems = SolarSystem::find(
                array(
                    $conditions,
                    "bind" => $parameters
                )
            );

            foreach ($solar_systems as $solar_system) {
                $solar_system->dele = 1;
                $success = $solar_system->save();
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
            $solar_system = SolarSystem::findFirst(
                array(
                    $conditions,
                    "bind" => $parameters
                )
            );
            $this->view->solar_system = $solar_system; 

            $galaxies = Galaxy::find(array(
                        "order" => "name"
                        ))->filter(
                function ($galaxy) {
                    if ($galaxy->cluster->dele == 0 && $galaxy->dele == 0) {
                        return $galaxy;
                    }
                }
            );
            $this->view->galaxis = $galaxies;
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

            $validation->add('id', new PresenceOf(array(
               'message' => 'Ой<br />'
            )));
            $validation->add('id', new StringLength(array(
                'max' => 9,
                'min' => 1,
                'messageMaximum' => 'ОЙ<br />',
                'messageMinimum' => 'ОЙ<br />'
            )));

            $validation->add('galaxy', new PresenceOf(array(
               'message' => 'Вы ввели пустую галактику<br />'
            )));

            $messages = $validation->validate($_POST);
            if (!count($messages)) 
            {                 
                global $name;
                global $id;
                $name = $this->request->getPost("name");
                $id = $this->request->getPost("id");
                $galaxy = $this->request->getPost("galaxy");

                $solar_system = SolarSystem::find(
                    array(
                        "order" => "name",
                    )
                )->filter(
                    function ($solar_system) {
                        global $name;
                        global $id;
                        if ($solar_system->galaxy->cluster->dele == 0 && $solar_system->galaxy->dele == 0 && $solar_system->dele == 0 && $solar_system->name == $name && $solar_system->id != $id ) {
                            return $solar_system;
                        }
                    }
                );
                if ($solar_system == false) 
                {
                    $conditions = "name = :name: AND dele = 0";

                    $parameters = array(
                    "name" => $galaxy);

                    $galaxy_id = Galaxy::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    $conditions = "name = :name:";

                    $parameters = array(
                    "name" => $type);

                    $conditions = "id = :id:";

                    $parameters = array(
                    "id" => $id);

                    $solar_system = SolarSystem::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );
                    try 
                    {

                        $solar_system->name = $name;
                        $solar_system->galaxy = $galaxy_id->id;
                        $success = $solar_system->save();
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
                else echo "Звёздная система с таким названием уже есть";
            
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
        $galaxies = Galaxy::find(array(
                        "order" => "name"
                        ))->filter(
                function ($galaxy) {
                    if ($galaxy->cluster->dele == 0 && $galaxy->dele == 0) {
                        return $galaxy;
                    }
                }
            );
        $this->view->galaxis = $galaxies;  
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
            
            $validation->add('galaxy', new PresenceOf(array(
               'message' => 'Вы ввели пустую галактику<br />'
            )));

            $messages = $validation->validate($_POST);
            if (!count($messages)) 
            {                 
                global $name;
                $name = $this->request->getPost("name");
                $galaxy = $this->request->getPost("galaxy");

                $solar_system = SolarSystem::find(
                    array(
                        "order" => "name",
                    )
                )->filter(
                    function ($solar_system) {
                        global $name;
                        if ($solar_system->galaxy->cluster->dele == 0 && $solar_system->galaxy->dele == 0 && $solar_system->dele == 0 && $solar_system->name == $name ) {
                            return $solar_system;
                        }
                    }
                );
                if ($solar_system == false) 
                {
                    $conditions = "name = :name: AND dele = 0";

                    $parameters = array(
                    "name" => $galaxy);

                    $galaxy_id = Galaxy::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    try 
                    {
                        $solar_system = new SolarSystem();
                        $solar_system->name = $name;
                        $solar_system->galaxy = $galaxy_id->id;
                        $success = $solar_system->save();
                        if ($success) {
                            echo "Чёрная дыра добавлена";
                        }
                        else
                            echo "Чёрная дыра не добавлена";
                    } 
                    catch (InvalidArgumentException $e) {
                        echo "Что-то пошло не так, пожалуйста проверьте корректность ввода";
                    }
                }
                else echo "Звёздная система с таким названием уже есть";
            
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

    public function IsAction()
    {
        if ($this->request->hasQuery("name") == true) {

            $validation = new Validation();
            $validation->add('name', new PresenceOf(array(
               'message' => 'Вы ввели пустое название<br>'
            )));
            $validation->add('name', new StringLength(array(
                'max' => 100,
                'min' => 1,
                'messageMaximum' => 'Вы ввели слишком большое название<br>',
                'messageMinimum' => 'Вы ввели слишком маленькое название<br>'
            )));
            $validation->add('name', new RegexValidator(array(
               'pattern' => '/[a-zA-Zа-яА-ЯЁё0-9]{1}[a-zA-Zа-яА-ЯЁё0-9\s]{0,99}/u',
               'message' => 'Введите название правильно<br>'
            )));
            $messages = $validation->validate($_GET);
            if (!count($messages)) 
            { 

                global $name;
                $name = $this->request->getQuery("name");
                $stars = Star::find(
                    array(
                        "order" => "name",
                    )
                )->filter(
                    function ($star) {
                        global $name;
                        if ($star->SolarSystem->Galaxy->cluster->dele == 0 && $star->SolarSystem->galaxy->dele == 0 && $star->SolarSystem->dele == 0 && $star->SolarSystem->name == $name ) {
                            return $star;
                        }
                    }
                );
                $planets = Planet::find(
                    array(
                        "order" => "name",
                    )
                )->filter(
                    function ($planet) {
                        global $name;
                        if ($planet->SolarSystem->Galaxy->cluster->dele == 0 && $planet->SolarSystem->galaxy->dele == 0 && $planet->SolarSystem->dele == 0 && $planet->SolarSystem->name == $name ) {
                            return $planet;
                        }
                    }
                );
                $this->view->ss_name = $name;
                $this->view->stars = $stars;
                $this->view->planets = $planets;  
            } 
            else
                foreach ($messages as $message) {
                    echo $message;
                }         

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
}


?>