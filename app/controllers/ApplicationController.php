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

    if (!is_array($tasks)) {
        $tasks = [];
    }

    // Si el primer elemento no es un array, lo convertimos en una lista de arrays
    if (!empty($tasks) && !is_array(reset($tasks))) {
        $tasks = [$tasks];
    }

    $this->view->tasks= $tasks;
}

function getFormAction (){
    
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

function editTaskAction()
{
    if (!isset($_GET['task_id'])) {
        $this->view->error = "ID de tarea no proporcionado.";
        return;
    }

    $taskId = $_GET['task_id'];
    $task = $this->taskModel->fetchTaskById($taskId);

    if ($task) {
        $this->view->task = $task;
    } else {
        $this->view->error = "Tarea no encontrada.";
    }
}

}

?>