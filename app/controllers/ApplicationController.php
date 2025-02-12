<?php
require_once __DIR__ . '/../lib/base/Controller.php';// probablemente esta ruta no es correcta
require_once __DIR__ . '/../app/models/Taskmodel.php';
/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicatonController extends Controller 
{

    private $taskModel;

    public function __construct()
    {
        $this->taskModel= new Taskmodel("data_tasks.json");
    }

function showDataAction(){

    $tasks =$this->taskModel->fetchAll();

    $this->view->tasks= $tasks;


}

function createTaskAction(){

    $tasks =$this->taskModel->fetchAll();

    $this->view->tasks= $tasks;




}
	

