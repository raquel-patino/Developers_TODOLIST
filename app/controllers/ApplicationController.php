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

    function createTaskAction() 
    { 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validación y sanitización de datos
            $taskData = [
                'title' => filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING),
                'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING),
                'state' => in_array($_POST['state'], ['pending', 'ongoing', 'ended']) ? $_POST['state'] : 'pending',
                'created_by' => filter_input(INPUT_POST, 'created_by', FILTER_SANITIZE_STRING),
                'start_time' => $this->validateDate($_POST['start_time']) ? $_POST['start_time'] : null,
                'end_time' => $this->validateDate($_POST['end_time']) ? $_POST['end_time'] : null,
            ];

            // Crear la tarea
            if ($this->taskModel->createTask($taskData)) {
                header('Location: ' . WEB_ROOT . '/');
                exit();
            } else {
                // Manejo de errores más robusto
                $this->view->error = "No se pudo crear la tarea.";
                exit();
            }
        }
    }

    // Método para validar formato de fecha
    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }


    function editTaskAction()
    {
        $id = $_POST["id"] ?? null;
        $task = $this->taskModel->fetchTaskById($id);

        if ($task) {
            $this->view->task = $task;
        } else {
            $_SESSION["error"] = "Tarea no encontrada.";
            header("Location: " . WEB_ROOT . "/");
            exit();
        }
    }

    function updateTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskData = [
                'id'          => htmlspecialchars($_POST['id']),
                'title'       => htmlspecialchars($_POST['title']),
                'description' => htmlspecialchars($_POST['description']),
                'state'       => in_array($_POST['state'], ['pending', 'ongoing', 'ended']) ? $_POST['state'] : 'pending',
                'created_by'  => htmlspecialchars($_POST['created_by']),
                'start_time'  => strtotime($_POST['start_time']) ? $_POST['start_time'] : null,
                'end_time'    => strtotime($_POST['end_time']) ? $_POST['end_time'] : null,
            ];

            if ($this->taskModel->updateTask($taskData)) {
                $_SESSION['popup_data'] = $taskData;
                header('Location: ' . WEB_ROOT . '/');
                exit();
            } else {
                $this->view->error = "No se pudo actualizar la tarea.";
            }
        }
    }

function deleteAction(){
    //comprobaciones de seguridad
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
        $_SESSION["error"]= "Método incorrecto";
        header("Location: " . WEB_ROOT . "/");
        exit();
    }
    if ((!isset($_POST["id"])) || empty($_POST["id"])){
            $_SESSION["error"]= "Esta tarea no se ha podido eliminar";
            header("Location: " . WEB_ROOT . "/");
            exit();
    }
        $id= $_POST["id"];
        $this->taskModel->deleteTask($id);
        $_SESSION["success"]= "La tarea se ha eliminado correctamente";
        header("Location: " . WEB_ROOT . "/");
        exit(); 
}
}

?>