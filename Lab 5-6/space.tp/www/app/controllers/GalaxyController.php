<?php

use Phalcon\Mvc\Controller;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Http\Response;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;


class GalaxyController extends Controller
{
    public function indexAction()
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
        if (count($galaxies) != 0) 
        {
            $validation_page = new Validation();
            $validation_page->add('page', new Between(array(
               'minimum' => 1,
               'maximum' => ceil(count($galaxies)/5),
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
                    "data"  => $galaxies,
                    "limit" => 5,
                    "page"  => $page
                )
            );

            $this->view->galaxies = $paginator->getPaginate();
        }
    }

    public function TypeAction()
    {
        $types = TypeOfGalaxy::find();
        $this->view->types = $types;
    }

    public function DeleteAction()
    {
        if ($this->request->isPost() == true) {

            $name = $this->request->getPost("name");

            $conditions = "name = :name:";

            $parameters = array(
            "name" => $name);

            $galaxies = Galaxy::find(
                array(
                    $conditions,
                    "bind" => $parameters
                )
            );

            foreach ($galaxies as $galaxy) {
                $galaxy->dele = 1;
                $success = $galaxy->save();
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
            $galaxy = Galaxy::findFirst(
                array(
                    $conditions,
                    "bind" => $parameters
                )
            );
            $this->view->galaxy = $galaxy; 

            $clusters = Cluster::find(
            array(
                "dele = 0"
                )); 
            $this->view->clusters = $clusters; 

            $types = TypeOfGalaxy::find();  
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
            $validation->add('size', new StringLength(array(
                'max' => 6,
                'min' => 1,
                'messageMaximum' => 'Такая большая галактика быть не может<br />',
                'messageMinimum' => 'Такая маленькая галактика быть не может<br />'
            )));
            $validation->add('size', new PresenceOf(array(
               'message' => 'Вы ввели пустой размер<br />'
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
            $validation->add('name', new RegexValidator(array(
               'pattern' => '/[a-zA-Zа-яА-ЯЁё0-9]{1}[a-zA-Zа-яА-ЯЁё0-9\s]{0,99}/u',
               'message' => 'Введите название правильно<br />'
            )));
            $validation->add('size', new RegexValidator(array(
               'pattern' => '/[0-9]{1,9}/',
               'message' => 'Введите размер правильно<br />'
            )));
            $validation->add('cluster', new PresenceOf(array(
               'message' => 'Вы ввели пустое скопление<br />'
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
                $size = $this->request->getPost("size");
                $id = $this->request->getPost("id");
                $cluster = $this->request->getPost("cluster");
                $type = $this->request->getPost("type");

                $galaxy = Galaxy::find()->filter(
                    function ($galaxy) {
                        global $name;
                        global $id;
                        if ($galaxy->cluster->dele == 0 && $galaxy->dele == 0 && $galaxy->name == $name && $galaxy->id != $id ) {
                            return $galaxy;
                        }
                    }
                );

                if ($galaxy == false) 
                {
                    $conditions = "name = :name: AND dele = 0";

                    $parameters = array(
                    "name" => $cluster);

                    $cluster_id = Cluster::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    $conditions = "name = :name:";

                    $parameters = array(
                    "name" => $type);

                    $type_id = TypeOfGalaxy::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    $conditions = "id = :id:";

                    $parameters = array(
                    "id" => $id);

                    $galaxy = Galaxy::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );
                    try 
                    {

                        $galaxy->name = $name;
                        $galaxy->size = $size;
                        $galaxy->type = $type_id->id;
                        $galaxy->cluster = $cluster_id->id;
                        $success = $galaxy->save();
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
                else echo "Галактика с таким названием уже есть";
            
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
        $clusters = Cluster::find(
            array(
                "dele = 0"
                )); 
        $this->view->clusters = $clusters; 

        $types = TypeOfGalaxy::find();  
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
            $validation->add('size', new StringLength(array(
                'max' => 6,
                'min' => 1,
                'messageMaximum' => 'Такая большая галактика быть не может<br />',
                'messageMinimum' => 'Такая маленькая галактика быть не может<br />'
            )));
            $validation->add('size', new PresenceOf(array(
               'message' => 'Вы ввели пустой размер<br />'
            )));
            $validation->add('name', new RegexValidator(array(
               'pattern' => '/[a-zA-Zа-яА-ЯЁё0-9]{1}[a-zA-Zа-яА-ЯЁё0-9\s]{0,99}/u',
               'message' => 'Введите название правильно<br />'
            )));
            $validation->add('size', new RegexValidator(array(
               'pattern' => '/[0-9]{1,9}/',
               'message' => 'Введите размер правильно<br />'
            )));
            $validation->add('cluster', new PresenceOf(array(
               'message' => 'Вы ввели пустое скопление<br />'
            )));
            $validation->add('type', new PresenceOf(array(
               'message' => 'Вы ввели пустой тип<br />'
            )));

            $messages = $validation->validate($_POST);
            if (!count($messages)) 
            {   
                global $name;             
                $name = $this->request->getPost("name");
                $size = $this->request->getPost("size");
                $cluster = $this->request->getPost("cluster");
                $type = $this->request->getPost("type");

                $galaxy = Galaxy::find()->filter(
                    function ($galaxy) {
                        global $name;
                        if ($galaxy->cluster->dele == 0 && $galaxy->dele == 0 && $galaxy->name == $name ) {
                            return $galaxy;
                        }
                    }
                );

                if ($galaxy == false) 
                {

                    $conditions = "name = :name: AND dele = 0";

                    $parameters = array(
                    "name" => $cluster);

                    $cluster_id = Cluster::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    $conditions = "name = :name:";

                    $parameters = array(
                    "name" => $type);

                    $type_id = TypeOfGalaxy::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    try 
                    {
                        $galaxy = new Galaxy();
                        $galaxy->name = $name;
                        $galaxy->size = $size;
                        $galaxy->type = $type_id->id;
                        $galaxy->cluster = $cluster_id->id;
                        $success = $galaxy->save();
                        if ($success) {
                            echo "Галактика добавлена";
                        }
                        else
                            echo "Галактика не добавлена";
                    } 
                    catch (Exception $e) {
                        echo "Что-то пошло не так, пожалуйста проверьте корректность ввода";
                    }
                }
                else echo "Галактика с таким названием уже есть";
            
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
                $black_holes = BlackHole::find(
                    array(
                        "order" => "name",
                    )
                )->filter(
                    function ($black_hole) {
                        global $name;
                        if ($black_hole->galaxy->cluster->dele == 0 && $black_hole->galaxy->dele == 0 && $black_hole->dele == 0 && $black_hole->galaxy->name == $name ) {
                            return $black_hole;
                        }
                    }
                );
                $solar_systems = SolarSystem::find(
                    array(
                        "order" => "name",
                    )
                )->filter(
                    function ($solar_system) {
                        global $name;
                        if ($solar_system->galaxy->cluster->dele == 0 && $solar_system->galaxy->dele == 0 && $solar_system->dele == 0 && $solar_system->galaxy->name == $name ) {
                            return $solar_system;
                        }
                    }
                );
                $this->view->galaxy_name = $name;
                $this->view->black_holes = $black_holes;
                $this->view->solar_systems = $solar_systems;  
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