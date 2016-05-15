<?php

use Phalcon\Mvc\Controller;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Http\Response;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;

class ClusterController extends Controller
{
    public function indexAction()
    {
        $clusters = Cluster::find(array(
                    "order" => "name"
                    ))->filter(
            function ($cluster) {
                if ($cluster->dele == 0) {
                    return $cluster;
                }
            }
        );
        if (count($clusters) != 0) 
        {
            $validation_page = new Validation();
            $validation_page->add('page', new Between(array(
               'minimum' => 1,
               'maximum' => ceil(count($clusters)/5),
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
                    "data"  => $clusters,
                    "limit" => 5,
                    "page"  => $page
                )
            );

            $this->view->clusters = $paginator->getPaginate();
        }
    }

    public function DeleteAction()
    {
        if ($this->request->isPost() == true) {

            $name = $this->request->getPost("name");

            $conditions = "name = :name:";

            $parameters = array(
            "name" => $name);

            $clusters = Cluster::find(
                array(
                    $conditions,
                    "bind" => $parameters
                )
            );

            foreach ($clusters as $cluster) {
                $cluster->dele = 1;
                $success = $cluster->save();
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

            $cluster = Cluster::findFirst(
                array(
                    $conditions,
                    "bind" => $parameters
                )
            );

            $this->view->cluster = $cluster;            

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
                'messageMaximum' => 'Вы ввели слишком большое название<br>',
                'messageMinimum' => 'Вы ввели слишком маленькое название<br>'
            )));
            $validation->add('kol', new StringLength(array(
                'max' => 6,
                'min' => 1,
                'messageMaximum' => 'Так много галактик быть не может<br>',
                'messageMinimum' => 'Так мало галактик быть не может<br>'
            )));
            $validation->add('name', new RegexValidator(array(
               'pattern' => '/[a-zA-Zа-яА-ЯЁё0-9]{1}[a-zA-Zа-яА-ЯЁё0-9\s]{0,99}/u',
               'message' => 'Введите название правильно<br>'
            )));
            $validation->add('kol', new RegexValidator(array(
               'pattern' => '/[0-9]{1,3}/',
               'message' => 'Введите кол-во правильно<br>'
            )));
            $validation->add('kol', new PresenceOf(array(
               'message' => 'Вы ввели пустое кол-во галактик<br>'
            )));
            $validation->add('id', new PresenceOf(array(
               'message' => 'Ой<br>'
            )));
            $validation->add('id', new StringLength(array(
                'max' => 9,
                'min' => 1,
                'messageMaximum' => 'ОЙ<br>',
                'messageMinimum' => 'ОЙ<br>'
            )));

            $messages = $validation->validate($_POST);
            if (!count($messages)) 
            {                 
                global $name;
                global $id;
                $name = $this->request->getPost("name");
                $kol = $this->request->getPost("kol");
                $id = $this->request->getPost("id");

                $cluster = Cluster::find()->filter(
                    function ($cluster) {
                        global $name;
                        global $id;
                        if ($cluster->dele == 0 && $cluster->name == $name && $cluster->id != $id ) {
                            return $cluster;
                        }
                    }
                );

                if ($cluster == false) 
                {
                    $conditions = "id = :id:";

                    $parameters = array(
                    "id" => $id);

                    $cluster = Cluster::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    try {
                        $cluster->name = $name;
                        $cluster->size = $kol;
                        $success = $cluster->save();
                        if ($success) {
                            echo "Данные упешно изменены<br>";
                        }
                        else
                            echo "Данные не изменены<br>";
                    } 
                    catch (InvalidArgumentException $e) {
                        echo "Что-то пошло не так, пожалуйста проверьте корректность ввода<br>";
                    }
                }
                else echo "Скопление с таким названием уже есть";
            }
            else
                foreach ($messages as $message) {
                    echo $message;
                }
        }
        else
            echo "Опаньки, поста то нет<br>";

        $this->view->disable();
    }

    public function InsertAction() {}

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
                'messageMaximum' => 'Вы ввели слишком большое название<br>',
                'messageMinimum' => 'Вы ввели слишком маленькое название<br>'
            )));
            $validation->add('kol', new StringLength(array(
                'max' => 6,
                'min' => 1,
                'messageMaximum' => 'Так много галактик быть не может<br>',
                'messageMinimum' => 'Так мало галактик быть не может<br>'
            )));
            $validation->add('kol', new PresenceOf(array(
               'message' => 'Вы ввели пустое кол-во галактик'
            )));
            $validation->add('name', new RegexValidator(array(
               'pattern' => '/[a-zA-Zа-яА-ЯЁё0-9]{1}[a-zA-Zа-яА-ЯЁё0-9\s]{0,99}/u',
               'message' => 'Введите название правильно<br>'
            )));
            $validation->add('kol', new RegexValidator(array(
               'pattern' => '/[0-9]{1,3}/',
               'message' => 'Введите кол-во правильно<br>'
            )));

            $messages = $validation->validate($_POST);
            if (!count($messages)) 
            {                 
                global $name;
                $name = $this->request->getPost("name");
                $kol = $this->request->getPost("kol");

                $cluster = Cluster::find()->filter(
                    function ($cluster) {
                        global $name;
                        if ($cluster->dele == 0 && $cluster->name == $name ) {
                            return $cluster;
                        }
                    }
                );

                if ($cluster == false) 
                {
                    try {
                        $cluster = new Cluster();
                        $cluster->name = $name;
                        $cluster->size = $kol;
                        $success = $cluster->save();
                        if ($success) {
                            echo "Скопление успешно добавлено<br>";
                        }
                        else
                            echo "Скопление не добавленно<br>";
                    } 
                    catch (InvalidArgumentException $e) {
                        echo "Что-то пошло не так, пожалуйста проверьте корректность ввода<br>";
                    }
                }
                else echo "Скопление с таким названием уже есть";
            }
            else
                foreach ($messages as $message) {
                    echo $message;
                }
        }
        else
            echo "Опаньки, поста то нет<br>";

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
                $galaxis = Galaxy::find(
                    array(
                        "order" => "name",
                    )
                )->filter(
                    function ($galaxy) {
                        global $name;
                        if ($galaxy->cluster->dele == 0 && $galaxy->dele == 0 && $galaxy->cluster->name == $name ) {
                            return $galaxy;
                        }
                    }
                );
                $this->view->cluster_name = $name;
                $this->view->galaxis = $galaxis;  
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