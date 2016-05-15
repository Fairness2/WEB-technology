<?php
header("Content-Type: text/html; charset=utf-8");

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Postgresql as DbAdapter;


try {

    // Регистрируем автозагрузчик
    $loader = new Loader();
    $loader->registerDirs(array(
        '../app/controllers/',
        '../app/models/'
    ))->register();

    // Создаем DI
    $di = new FactoryDefault();

     // Настраиваем сервис для работы с БД
    $di->set('db', function () {
        return new DbAdapter(array(
            "host"     => "localhost",
            "username" => "postgres",
            "password" => "postgres",
            "dbname"   => "tptptp"
        ));
    });

    // Настраиваем компонент View
    $di->set('view', function () {
        $view = new View();
        $view->setViewsDir('../app/views/');
        return $view;
    });

    // Настраиваем базовый URI так, чтобы все генерируемые URI содержали директорию "tutorial"
    $di->set('url', function () {
        $url = new UrlProvider();
        $url->setBaseUri('');
        return $url;
    });

    
    // Обрабатываем запрос
    $application = new Application($di);

    echo $application->handle()->getContent();

} catch (\Exception $e) {
     echo "Exception: ", $e->getMessage();
}

?>