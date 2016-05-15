<?php
use Phalcon\Mvc\Controller;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Http\Response;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;
use Phalcon\Paginator\Adapter\Model as Paginator;


class BlackHoleController extends Controller
{
    public function indexAction()
    {
        ini_set('memory_limit', '2000M');
        ini_set("max_execution_time", "2900");
        $black_holes = BlackHole::find(array(
                    "order" => "name"
                    ))->filter(
            function ($black_hole) {
                if ($black_hole->galaxy->cluster->dele == 0 && $black_hole->galaxy->dele == 0 && $black_hole->dele == 0) {
                    return $black_hole;
                }
            }
        );
        if (count($black_holes) != 0) 
        {
            $validation_page = new Validation();
            $validation_page->add('page', new Between(array(
               'minimum' => 1,
               'maximum' => ceil(count($black_holes)/5),
               'message' => '1'
            )));

            $page = 1;        
            $messages = $validation_page->validate($_GET);
            if (!count($messages))
            {
                $page = $this->request->get("page");
            }

            $paginator = new Paginator(
                array(
                    "data"  => $black_holes,
                    "limit" => 5,
                    "page"  => $page
                )
            );

            $this->view->black_holes = $paginator->getPaginate();
        }
    }

    public function TypeAction()
    {
        $types = TypeOfBlackHole::find();
        $this->view->types = $types;
    }

    public function DeleteAction()
    {
        ini_set('memory_limit', '2000M');
        ini_set("max_execution_time", "2900");
        if ($this->request->isPost() == true) {

            $name = $this->request->getPost("name");

            $conditions = "name = :name:";

            $parameters = array(
            "name" => $name);

            $black_holes = BlackHole::find(
                array(
                    $conditions,
                    "bind" => $parameters
                )
            );

            foreach ($black_holes as $black_hole) {
                $black_hole->dele = 1;
                $success = $black_hole->save();
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
        ini_set('memory_limit', '2000M');
        ini_set("max_execution_time", "2900");
        if ($this->request->isPost() == true) {

            $id = $this->request->getPost("id");

            $conditions = "id = :id:";

            $parameters = array(
            "id" => $id);
            $black_hole = BlackHole::findFirst(
                array(
                    $conditions,
                    "bind" => $parameters
                )
            );
            $this->view->black_hole = $black_hole; 

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

            $types = TypeOfBlackHole::find();  
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
        ini_set('memory_limit', '2000M');
        ini_set("max_execution_time", "2900");
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

            $validation->add('id', new PresenceOf(array(
               'message' => 'Ой<br />'
            )));
            $validation->add('id', new StringLength(array(
                'max' => 9,
                'min' => 1,
                'messageMaximum' => 'ОЙ<br />',
                'messageMinimum' => 'ОЙ<br />'
            )));

            $validation->add('age', new StringLength(array(
                'max' => 6,
                'min' => 1,
                'messageMaximum' => 'Такая большой возраст быть не может<br />',
                'messageMinimum' => 'Такая маленький возраст быть не может<br />'
            )));
            $validation->add('age', new PresenceOf(array(
               'message' => 'Вы ввели пустой возраст<br />'
            )));
            $validation->add('age', new RegexValidator(array(
               'pattern' => '/[0-9]{1,9}/',
               'message' => 'Введите возраст правильно<br />'
            )));            
            
            $validation->add('galaxy', new PresenceOf(array(
               'message' => 'Вы ввели пустую галактику<br />'
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
                $age = $this->request->getPost("age");
                $id = $this->request->getPost("id");
                $galaxy = $this->request->getPost("galaxy");
                $type = $this->request->getPost("type");

                $black_holes = BlackHole::find(
                    array(
                        "order" => "name",
                    )
                )->filter(
                    function ($black_hole) {
                        global $name;
                        global $id;
                        if ($black_hole->galaxy->cluster->dele == 0 && $black_hole->galaxy->dele == 0 && $black_hole->dele == 0 && $black_hole->name == $name && $black_hole->id != $id ) {
                            return $black_hole;
                        }
                    }
                );

                if ($galaxy == false) 
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

                    $type_id = TypeOfBlackHole::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    $conditions = "id = :id:";

                    $parameters = array(
                    "id" => $id);

                    $black_hole = BlackHole::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );
                    try 
                    {

                        $black_hole->name = $name;
                        $black_hole->weight = $weight;
                        $black_hole->age = $age;
                        $black_hole->type = $type_id->id;
                        $black_hole->galaxy = $galaxy_id->id;
                        $success = $black_hole->save();
                        if ($success) {
                            echo "Данные упешно изменены";
                        }
                        else
                            echo "Данные не изменены";
                    } 
                    catch (Exception $e) {
                        echo "Что-то пошло не так, пожалуйста проверьте корректность ввода";
                    }
                }
                else echo "Чёрная дыра с таким названием уже есть";
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

        $types = TypeOfBlackHole::find();  
        $this->view->types = $types;  
    }

    public function EnterAction()
    {
        ini_set('memory_limit', '2000M');
        ini_set("max_execution_time", "2900");
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

            $validation->add('age', new StringLength(array(
                'max' => 6,
                'min' => 1,
                'messageMaximum' => 'Такая большой возраст быть не может<br />',
                'messageMinimum' => 'Такая маленький возраст быть не может<br />'
            )));
            $validation->add('age', new PresenceOf(array(
               'message' => 'Вы ввели пустой возраст<br />'
            )));
            $validation->add('age', new RegexValidator(array(
               'pattern' => '/[0-9]{1,9}/',
               'message' => 'Введите возраст правильно<br />'
            )));            
            
            $validation->add('galaxy', new PresenceOf(array(
               'message' => 'Вы ввели пустую галактику<br />'
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
                $age = $this->request->getPost("age");
                $id = $this->request->getPost("id");
                $galaxy = $this->request->getPost("galaxy");
                $type = $this->request->getPost("type");

                $black_holes = BlackHole::find(
                    array(
                        "order" => "name",
                    )
                )/*->filter(
                    function ($black_hole) {
                        global $name;
                        if ($black_hole->galaxy->cluster->dele == 0 && $black_hole->galaxy->dele == 0 && $black_hole->dele == 0 && $black_hole->name == $name) {
                            return $black_hole;
                        }
                    }
                )*/;

                if ($black_holes == false) 
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

                    $type_id = TypeOfBlackHole::findFirst(
                        array(
                            $conditions,
                            "bind" => $parameters
                        )
                    );

                    try 
                    {
                        $black_hole = new BlackHole();
                        $black_hole->name = $name;
                        $black_hole->weight = $weight;
                        $black_hole->age = $age;
                        $black_hole->type = $type_id->id;
                        $black_hole->galaxy = $galaxy_id->id;
                        $success = $black_hole->save();
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
                else echo "Чёрная дыра с таким названием уже есть";
            
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

    public function ExcelAction()
    {
        ini_set('memory_limit', '2000M');
        ini_set("max_execution_time", "2900");
        // Подключаем класс для работы с excel
        require_once('PHPExcel.php');
        // Подключаем класс для вывода данных в формате excel
        require_once('PHPExcel/Writer/Excel5.php');

        // Создаем объект класса PHPExcel
        $xls = new PHPExcel();
        // Устанавливаем индекс активного листа
        $xls->setActiveSheetIndex(0);
        // Получаем активный лист
        $sheet = $xls->getActiveSheet();
        // Подписываем лист
        $sheet->setTitle('Таблица умножения');

        // Вставляем текст в ячейку A1
        $sheet->setCellValue("A1", 'Название');
        // Вставляем текст в ячейку B1
        $sheet->setCellValue("B1", 'Вес в массах Солнца');
        // Вставляем текст в ячейку C1
        $sheet->setCellValue("C1", 'Тип');
        // Вставляем текст в ячейку D1
        $sheet->setCellValue("D1", 'Возраст в миллиардах лет');
        // Вставляем текст в ячейку E1
        $sheet->setCellValue("E1", 'Галактика');
        $sheet->getStyle('A1:E1')->getFill()->setFillType(
            PHPExcel_Style_Fill::FILL_SOLID);
        $sheet->getStyle('A1:E1')->getFill()->getStartColor()->setRGB('EEEEEE');
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);

        // Выравнивание текста
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(
            PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $black_holes = BlackHole::find(array(
                    "order" => "name"
                    ))->filter(
            function ($black_hole) {
                if ($black_hole->galaxy->cluster->dele == 0 && $black_hole->galaxy->dele == 0 && $black_hole->dele == 0) {
                    return $black_hole;
                }
            }
        );

        if (count($black_holes) != 0) 
        {
            
            $count = count($black_holes);
            $i = 2;
            foreach ($black_holes as $black_hole)
            {
                $sheet->setCellValueByColumnAndRow(
                                                  0,
                                                  $i,
                                                  $black_hole->name);
                $sheet->setCellValueByColumnAndRow(
                                                  1,
                                                  $i,
                                                  $black_hole->weight);
                $sheet->setCellValueByColumnAndRow(
                                                  2,
                                                  $i,
                                                  $black_hole->typeOfBlackHole->name);
                $sheet->setCellValueByColumnAndRow(
                                                  3,
                                                  $i,
                                                  $black_hole->age);
                $sheet->setCellValueByColumnAndRow(
                                                  4,
                                                  $i,
                                                  $black_hole->galaxy->name);
                $i++;
            }            
        }

        //for ($i = 2; $i < 10; $i++) {
        //    for ($j = 2; $j < 10; $j++) {
        //        // Выводим таблицу умножения
        //        $sheet->setCellValueByColumnAndRow(
        //                                          $i - 2,
        //                                          $j,
        //                                          $i . "x" .$j . "=" . ($i*$j));
        //        // Применяем выравнивание
        //        $sheet->getStyleByColumnAndRow($i - 2, $j)->getAlignment()->
        //                setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //    }
        //}

        // Выводим HTTP-заголовки
        header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
        header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
        header ( "Cache-Control: no-cache, must-revalidate" );
        header ( "Pragma: no-cache" );
        header ( "Content-type: application/vnd.ms-excel" );
        header ( "Content-Disposition: attachment; filename=matrix.xls" );

        // Выводим содержимое файла
        $objWriter = new PHPExcel_Writer_Excel5($xls);
        $objWriter->save('php://output');
        $this->view->disable();

    }


}


?>