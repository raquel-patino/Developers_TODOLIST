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

/*
<?php

class JsonModel
{
    protected $_filePath = "";
    protected $_data = array();

    public function __construct($file)
    {
        $this->_filePath = __DIR__ . '/../data/' . $file;
        $this->loadData();
    }

    protected function loadData()
    {
        if (is_file($this->_filePath)) {
            $json = file_get_contents($this->_filePath);
            $this->_data = json_decode($json, true) ?? [];
        } else {
            $this->_data = [];
        }
    }

    protected function saveData()
    {
        file_put_contents($this->_filePath, json_encode($this->_data, JSON_PRETTY_PRINT));
    }

    public function save($data)
    {
        if (!isset($data['id'])) {
            $data['id'] = $this->generateId();
            $this->_data[] = $data;
        } else {
            foreach ($this->_data as &$item) {
                if ($item['id'] == $data['id']) {
                    $item = array_merge($item, $data);
                    break;
                }
            }
        }
        $this->saveData();
        return $data['id'];
    }

    protected function generateId()
    {
        $ids = array_column($this->_data, 'id');
        return empty($ids) ? 1 : max($ids) + 1;
    }

    public function fetchAll()
    {
        return $this->_data;
    }
}
    */


}


?>