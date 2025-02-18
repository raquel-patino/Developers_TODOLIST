<?php

class Taskmodel{

protected $filePath= "";
protected $data= [];

function __construct($file)
{
    
    $this->filePath= ROOT_PATH . '/data/'. $file;
    $this->loadData();

}

protected function loadData()
{
    if (is_file($this->filePath)) {
        $json = file_get_contents($this->filePath);
        $this->data = json_decode($json, true);
        
        if ($this->data === null) {
            die("Error al leer JSON: " . json_last_error_msg());
        }
    } else {
        die("No se encontró el archivo JSON: " . $this->filePath);
    }
}

public function fetchAll()
    {
        
        return $this->data;
    }


    protected function saveData() 
    {
        return file_put_contents($this->filePath, json_encode($this->data, JSON_PRETTY_PRINT))!== false;
    }


}

?>