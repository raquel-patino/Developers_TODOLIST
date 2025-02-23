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
            $taskData = [
                'title'       => $this->sanitizeText($_POST['title'], 50),
                'description' => $this->sanitizeText($_POST['description'], 500),
                'state'       => in_array($_POST['state'], ['pending', 'ongoing', 'ended']) ? $_POST['state'] : 'pending',
                'created_by'  => $this->sanitizeText($_POST['created_by'], 30),
                'start_time'  => $this->sanitizeDate($_POST['start_time']),
                'end_time'    => $this->sanitizeDate($_POST['end_time']),
            ];
            if ($this->taskModel->createTask($taskData)) {
                header('Location: ' . WEB_ROOT . '/');
                exit();
            } else {
                $this->view->error = "No se pudo crear la tarea.";
                exit();
            }
        }
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
                'title'       => $this->sanitizeText($_POST['title'], 50),
                'description' => $this->sanitizeText($_POST['description'], 500),
                'state'       => in_array($_POST['state'], ['pending', 'ongoing', 'ended']) ? $_POST['state'] : 'pending',
                'created_by'  => $this->sanitizeText($_POST['created_by'], 30),
                'start_time'  => $this->sanitizeDate($_POST['start_time']),
                'end_time'    => $this->sanitizeDate($_POST['end_time']),
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

    private function sanitizeText($text, $maxLength)
    {
        $text = trim($text);
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        return mb_substr($text, 0, $maxLength);
    }

    private function sanitizeDate($date)
    {
        if (empty($date)) {
            return null;
        }
        $date = str_replace('T', ' ', $date);
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return null;
        }
        return date('d-m-Y H:i', $timestamp);
    }

    function deleteConfirmationAction()
    {
        $id = $_POST["id"] ?? null;
        $task = $this->taskModel->fetchTaskById($id);
    
        if ($task) {
            $_SESSION['delete_popup'] = true; // Establece la sesión para mostrar el pop-up
            $_SESSION['delete_task_id'] = $task['id']; // Almacena el ID de la tarea
            $_SESSION['delete_task_title'] = $task['title']; // Almacena el título de la tarea
            header("Location: " . WEB_ROOT . "/"); // Redirige a la vista de lista de tareas
            exit();
        } else {
            $_SESSION["error"] = "Tarea no encontrada.";
            header("Location: " . WEB_ROOT . "/");
            exit();
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

function searchAction(){
    //comprobacion y sanitización de carácteres introducidos
        if(isset($_GET["search"])){
            $search= trim($_GET["search"]);
            $search= htmlspecialchars($search,ENT_QUOTES, 'UTF-8');
        }
     
        $searchModified= iconv('UTF-8', 'ASCII//TRANSLIT', $search);
        $searchNoAccents = str_replace(["'", "`", "^", "~"], "", $searchModified);
        $this->taskModel->searchTask($searchNoAccents);
    
        if (count($this->taskModel->searchTask($searchNoAccents)) > 0){
            $this->view->tasks= $this->taskModel->searchTask($searchNoAccents);
    
        }else{
            $_SESSION["error"]= "No se ha encontrado ninguna tarea con este título";
            header("Location: " . WEB_ROOT . "/");
            exit(); 
        }
    
    
    }
}

?>