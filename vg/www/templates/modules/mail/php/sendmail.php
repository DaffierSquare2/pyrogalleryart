<?php
// Подключим composer, defines
require_once __DIR__ .'/../../../../../system/defines.php';
require_once __DIR__ .'/../../../../../system/vendor/autoload.php';

// e-mail кому отправить
$emailto = _ADMIN_EMAIL_;

// Получим данные
$data = $_POST;

// Если включена капча, то проверяем капчу
if(_RECAPTCHA_){
    if(!\core\ReCaptcha::instance()->checkRecaptcha()){
        echo json_encode(['error' => 1, 'data' => 'Error -> Captcha']);
        exit();
    }
}

// Проверим поле на пустоту
// if(empty($data['name'])){
//     echo json_encode(['error' => 1, 'data' => 'Enter your Name']);
//     exit();
// }
// if(empty($data['email'])){
//     echo json_encode(['error' => 1, 'data' => 'Enter your Email']);
//     exit();
// }
// if(empty($data['message'])){
//     echo json_encode(['error' => 1, 'data' => 'Enter your message']);
//     exit();
// }

//Сохраним файл
$attachments=[];
$uploaddir =  __DIR__.'/../../../../../temp/';
if (!file_exists($uploaddir)) {
    mkdir($uploaddir, 0777, true);
}
if(!empty($_FILES['file']['tmp_name'])){
    $uploadfile = $uploaddir . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
        // echo "Файл корректен и был успешно загружен.\n";
    } else {
        echo json_encode(['error' => 1, 'data' => 'Возможная атака с помощью файловой загрузки!']);
        exit();
    }
    $attachments[]=$uploadfile;
}

$message = '<p>Ниже прикреплены контакты и пожелания заказчика:</p>';

// Прикрепим все данные из формы
$message .= \modules\mail\services\sMail::instance()->getBlockBuffer($data);


\core\PHPMail::instance()->sendSMTPMail($emailto, 'Новая заявка с сайта '.$_SERVER['HTTP_HOST'], $message, $attachments);

echo json_encode(['error' => 0, 'data' => 'Message sent successfully!']);
exit();