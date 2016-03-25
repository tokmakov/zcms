<?php
/**
 * Класс Error отвечает за обработку ошибок, возникающих при работе приложения
 */
class Error extends Base {

    /**
     * полная информация об ошибке; код ошибки; сообщение об ошибке; файл,
     * в котором произошла ошибка; строка, в которой произошла ошибка;
     * стек вызовов до точки, в которой произошла ошибка
     */
    private $errorInfo, $errorCode, $errorMessage, $errorFile, $errorLine, $errorTrace;


    public function __construct($e) {

        parent::__construct();

        /*
         * полная информация об ошибке; код ошибки; сообщение об ошибке; файл,
         * в котором произошла ошибка; строка, в которой произошла ошибка;
         * стек вызовов до точки, в которой произошла ошибка
         */
        $this->errorCode = $e->getCode();
        $this->errorMessage = $e->getMessage();
        $this->errorFile = $e->getFile();
        $this->errorLine = $e->getLine();
        $this->errorTrace = $e->getTraceAsString();
        // ошибка: дата, код, текст, файл, строка, стек вызовов
        $this->errorInfo  = date('d.m.Y H:i:s') . PHP_EOL;
        $this->errorInfo .= 'Код: ' . $this->errorCode . ', текст: ' . $this->errorMessage . PHP_EOL;
        $this->errorInfo .= 'Файл: ' . $this->errorFile . ', строка: ' . $this->errorLine . PHP_EOL;
        $this->errorInfo .= 'Стек вызовов: ' . PHP_EOL . $this->errorTrace . PHP_EOL . PHP_EOL;
        // записать ошибку в лог?
        if ($this->config->error->write) {
            file_put_contents($this->config->error->file, $this->errorInfo, FILE_APPEND);
        }
        // отправить письмо администратору?
        if ($this->config->error->sendmail) {
            $subject = '=?utf-8?B?'.base64_encode('Произошла ошибка').'?=';
            $headers = 'From: <'.$this->config->email->site.'>'."\r\n";
            $headers = $headers.'Date: '.date( 'r' )."\r\n";
            $headers = $headers.'Content-type: text/plain; charset="utf-8"'."\r\n";
            $headers = $headers.'Content-Transfer-Encoding: base64';
            $message = chunk_split(base64_encode($this->errorInfo));
            mail($this->config->email->admin, $subject, $message, $headers);
        }

        // сообщение об ошибке для посетителей сайта
        $message = $this->config->message->error;
        // сообщение об ошибке для администратора (разработчика) сайта
        if ($this->config->error->debug) {
            $message = nl2br(htmlspecialchars($this->errorInfo));
        }

        // получаем html-код страницы ошибки
        $backfront = ($this->backend) ? 'backend' : 'frontend';
        ob_start();
        require $this->config->site->theme . '/' . $backfront . '/template/error.php';
        $content = ob_get_clean();

        // отправляем заголовки
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Length: ' . strlen($content));

        // выводим сформированную страницу в браузер
        echo $content;

    }

}