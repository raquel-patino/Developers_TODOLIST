<?php

class Taskmodel {

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

    public function fetchTaskById($id) 
    {
        foreach ($this->data as $task) {
            if ($task['id'] == $id) {
                return $task;
            }
        }
        return null;
    }

    public function createTask(array $taskData) 
    {
        $newTask = [
            'id' => $this->generateId(),
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'state' => $taskData['state'],
            'created_by' => $taskData['created_by'],
            'start_time' => $taskData['start_time'],
            'end_time' => $taskData['end_time'],
        ];       
        array_unshift($this->data,$newTask);
        $this->saveData(); //eliminar?
        return $this->saveData();
    }
    
    protected function generateId() 
    {
        $ids = array_column($this->data, 'id');
        return empty($ids) ? 1 : max($ids) + 1;
    }

    protected function saveData() 
    {
        return file_put_contents($this->filePath, json_encode($this->data, JSON_PRETTY_PRINT))!== false;
    }
    
    public function updateTask(array $taskData)
    {
        foreach ($this->data as &$task) {
            if ($task['id'] == $taskData['id']) {
                $task['title']       = $taskData['title'];
                $task['description'] = $taskData['description'];
                $task['state']       = $taskData['state'];
                $task['created_by']  = $taskData['created_by'];
                $task['start_time']  = $taskData['start_time'];
                $task['end_time']    = $taskData['end_time'];
                return $this->saveData();
            }
        }
        return false;
    }  
    
public function deleteTask($id){
        $tasksReduced= [];
        foreach ($this->data as $task){
            if ($task["id"] != $id){
                $tasksReduced[]= $task;
            }
        }
        $this->data= $tasksReduced;
        $this->saveData();
    
    }

    public function searchTask($taskSearched){
        $tasksFound = [];
        
        foreach ($this->data as $task){
            $titleModified= iconv('UTF-8', 'ASCII//TRANSLIT', $task["title"]); //convierto para eliminar acentos 
            $titleNoAccents = str_replace(["'", "`", "^", "~"], "", $titleModified);
            if (stripos($titleNoAccents, $taskSearched) !== false){
                $tasksFound[]=$task;
            }
        } 
        return $tasksFound;
    
    }

}

?>