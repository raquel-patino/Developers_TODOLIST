<?php
require_once ROOT_PATH . '/lib/base/Controller.php';
require_once ROOT_PATH . '/app/models/TaskModel.php';

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller {

    private $taskModel;

    public function __construct()
    {
        $this->taskModel= new Taskmodel("data_tasks.json");
    }

function showDataAction(){

    $tasks =$this->taskModel->fetchAll();

    $this->view->tasks= $tasks;


}

function createTaskAction() 
{ 
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $taskData = [
            'title' => htmlspecialchars($_POST['title']),
            'description' => htmlspecialchars($_POST['description']),
            'state' => in_array($_POST['state'], ['pendiente', 'en progreso', 'completado']) ? $_POST['state'] : 'pendiente',
            'created_by' => htmlspecialchars($_POST['created_by']),
            'start_time' => strtotime($_POST['start_time']) ? $_POST['start_time'] : null,
            'end_time' => strtotime($_POST['end_time']) ? $_POST['end_time'] : null,
        ];

        if ($this->taskModel->createTask($taskData)) {
            header('Location: ' . WEB_ROOT . '/');
            exit();
        } else {
            $this->view->error = "No se pudo crear la tarea.";
        }
    }
}

}

?>