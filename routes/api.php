<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AddMaterialController;
use App\Http\Controllers\TypeEquipmentController;
use App\Http\Controllers\StatusEquipmentController;
use App\Http\Controllers\ConstructionEquipmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommunicationTypeController;
use App\Http\Controllers\JobStatusController;
use App\Http\Controllers\JobPrioritiesController;
use App\Http\Controllers\TypeMaintenanceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WorkOrdersController;
use App\Http\Controllers\AssignedWorkerController;
use App\Http\Controllers\MaterialAssignedController;
use App\Http\Controllers\EquipmentAssignedController;
use App\Http\Controllers\ReportMaterialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MagnitudeController;
use App\Http\Controllers\MagnitudeValueController;
use App\Http\Controllers\WorkOrderStatusesController;


/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/


//---------------------------------------   RUTAS PROTEGIDAS ------------------------------------------------------

Route::group(['middleware' => ['auth:sanctum']], function () {

    //Rutas de usuario
    Route::post('v1/user/logout', [UserController::class, 'logout']);
    Route::post('v1/user/createAdmin', [UserController::class, 'createAdmin']);
    Route::get('v1/user/userProfile', [UserController::class, 'userProfile']);
    Route::get('v1/user/userNoRole', [UserController::class, 'getUserNoRole']);
    Route::get('v1/user/userRol/{id_user}/{id_department}/{id_rol}', [UserController::class, 'getUserRol']);
    Route::get('v1/user/userNoRol/department', [UserController::class, 'getUsersDepartmentNoRol']);
    Route::patch('v1/user/updateUserRole/{id}', [UserController::class, 'updateUserRole']);
    Route::patch('v1/user/updateUserProfile/{id}', [UserController::class, 'updateUserProfile']);
    Route::patch('v1/user/deleteProfileImage/{id}', [UserController::class, 'deleteProfileImage']);
    Route::delete('v1/user/deleteUser/{id}', [UserController::class, 'deleteUser']);
    Route::patch('v1/user/deleteLogicUser/{id}', [UserController::class, 'deleteLogicUser']);
    Route::get('v1/user/invalidUser', [UserController::class, 'getInvalidUser']);
    Route::get('v1/user/invaliduser/department', [UserController::class, 'getInvalidUserDepartment']);
    Route::get('v1/user/userAvailable', [UserController::class, 'getUserAvailable']);
    Route::get('v1/user/userNoAvailable', [UserController::class, 'getUserNoAvailable']);
    Route::get('v1/user/getUserAvailableEdit', [UserController::class, 'getUserAvailableEdit']);

    //Rutas de rol
    Route::get('v1/rol/getById/{id}', [RolController::class, 'getRolById']);

    //Rutas de departamento
    Route::post('v1/department', [DepartmentController::class, 'create']);
    Route::get('v1/department/departmentusers', [DepartmentController::class, 'getDepartmentUsers']);
    Route::get('v1/department/nameDepartment/{id}', [DepartmentController::class, 'getNameDepartmentById']);
    Route::get('v1/departments/departmentusers/noAdmin', [DepartmentController::class, 'getDepartment']);
    Route::get('v1/departments/jobs', [DepartmentController::class, 'getAssignDepartment']);
    Route::get('v1/departmentsById/{id}', [DepartmentController::class, 'getById']);
    Route::get('v1/departments/jobs/{id}', [DepartmentController::class, 'getAssignDepartmentByID']);
    Route::put('v1/department/{id}', [DepartmentController::class, 'update']);
    Route::delete('v1/department/{id}', [DepartmentController::class, 'delete']);


    //Rutas de Establecimientos
    Route::post('v1/establishment', [EstablishmentController::class, 'createEstablishment']);
    Route::get('v1/establishment', [EstablishmentController::class, 'getEstablishment']);
    Route::delete('v1/establishment/{id}', [EstablishmentController::class, 'delete']);
    Route::put('v1/establishment/{id}', [EstablishmentController::class, 'update']);
    

    //Rutas de Materiales
    Route::post('v1/material', [MaterialController::class, 'createMaterial']);
    Route::get('v1/material', [MaterialController::class, 'getMaterial']);
    Route::get('v1/materialStock', [MaterialController::class, 'getMaterialStock']);
    Route::delete('v1/material/{id}', [MaterialController::class, 'deleteMaterial']);
    Route::get('v1/materialById/{id}', [MaterialController::class, 'getMaterialById']);
    Route::put('v1/materialById/{id}', [MaterialController::class, 'updateMaterial']);
    Route::post('v1/materialNotification', [MaterialController::class, 'notiMaterialUnavailable']);

    //Rutas para AddMaterial
    Route::post('v1/materialAdd', [AddMaterialController::class, 'addStock']);
    Route::get('v1/materialAdd', [AddMaterialController::class, 'get']);
    Route::get('v1/materialAdd/{id}', [AddMaterialController::class, 'getAddByMaterialId']);
    Route::put('v1/materialAdd/{id}', [AddMaterialController::class, 'update']);
    Route::delete('v1/materialAdd/{id}', [AddMaterialController::class, 'deleteAddMaterial']);

    //Rutas para typeEquipment
    Route::post('v1/typeEquipment', [TypeEquipmentController::class, 'createTypeEquipment']);
    Route::get('v1/typeEquipment', [TypeEquipmentController::class, 'getTypeEquipment']);
    Route::delete('v1/typeEquipment/{id}', [TypeEquipmentController::class, 'deleteTypeEquipment']);
    Route::put('v1/typeEquipment/{id}', [TypeEquipmentController::class, 'updateTypeEquipment']);

    //Rutas para statusEquipment
    Route::post('v1/statusEquipment', [StatusEquipmentController::class, 'createStatus']);
    Route::get('v1/statusEquipment', [StatusEquipmentController::class, 'getStatus']);
    Route::put('v1/statusEquipment/{id}', [StatusEquipmentController::class, 'updateStatus']);
    Route::delete('v1/statusEquipment/{id}', [StatusEquipmentController::class, 'deleteStatus']);

    //Rutas para equipment
    Route::post('v1/equipment', [ConstructionEquipmentController::class, 'createEquipment']);
    Route::get('v1/equipment/{id_status_equipment?}/{id_type_equipment?}', [ConstructionEquipmentController::class, 'getEquipmentByStatusType']);
    Route::get('v1/equipmentById/{id}', [ConstructionEquipmentController::class, 'getEquipmentById']);
    Route::put('v1/equipment/{id}', [ConstructionEquipmentController::class, 'updateEquipment']);
    Route::delete('v1/equipment/{id}', [ConstructionEquipmentController::class, 'deleteEquipment']);
    Route::post('v1/equipmentNotification', [ConstructionEquipmentController::class, 'notiEquipmentUnavailable']);

    //Rutas para client
    Route::post('v1/client', [ClientController::class, 'create']);
    Route::get('v1/client', [ClientController::class, 'get']);
    Route::get('v1/clientDelete', [ClientController::class, 'getDelete']);
    Route::put('v1/client/{id}', [ClientController::class, 'update']);
    Route::delete('v1/client/{id}', [ClientController::class, 'deleteLogic']);
    Route::patch('v1/client_restore/{id}', [ClientController::class, 'restoreClient']);

    //Rutas para communication_type
    Route::post('v1/communication_type', [CommunicationTypeController::class, 'create']);
    Route::get('v1/communication_type', [CommunicationTypeController::class, 'get']);
    Route::put('v1/communication_type/{id}', [CommunicationTypeController::class, 'update']);
    Route::delete('v1/communication_type/{id}', [CommunicationTypeController::class, 'delete']);

    //Rutas para job_statuses
    Route::post('v1/job_statuses', [JobStatusController::class, 'create']);
    Route::get('v1/job_statuses', [JobStatusController::class, 'get']);
    Route::get('v1/jobByStep', [JobStatusController::class, 'getSteps']);
    Route::get('v1/jobByStep/{id}', [JobStatusController::class, 'getStatusJob']);
    Route::put('v1/job_statuses/{id}', [JobStatusController::class, 'update']);
    Route::delete('v1/job_statuses/{id}', [JobStatusController::class, 'delete']);

    //Rutas para job_priorities
    Route::post('v1/job_priorities', [JobPrioritiesController::class, 'create']); 
    Route::get('v1/job_priorities', [JobPrioritiesController::class, 'get']); 
    Route::put('v1/job_priorities/{id}', [JobPrioritiesController::class, 'update']);
    Route::delete('v1/job_priorities/{id}', [JobPrioritiesController::class, 'delete']);  

    //Rutas para type_maintenance
    Route::post('v1/type_maintenance', [TypeMaintenanceController::class, 'create']);
    Route::get('v1/type_maintenance', [TypeMaintenanceController::class, 'get']);
    Route::put('v1/type_maintenance/{id}', [TypeMaintenanceController::class, 'update']);
    Route::delete('v1/type_maintenance/{id}', [TypeMaintenanceController::class, 'delete']);

    //Rutas para job
    Route::post('v1/job', [JobController::class, 'create']); 
    Route::get('v1/job', [JobController::class, 'get']); 
    Route::get('v1/jobDelete', [JobController::class, 'getDelete']); 
    Route::get('v1/jobByDate', [JobController::class, 'getByDate']);
    Route::get('v1/jobByStatusOrDate', [JobController::class, 'getJobByStatusOrDate']);
    Route::get('v1/jobByID/{id}', [JobController::class, 'getById']);
    Route::get('v1/jobById/join/{id}', [JobController::class, 'getJobById']);
    Route::get('v1/job/department/{id}', [JobController::class, 'getDepartmentsAssignedToJob']);
    Route::get('v1/job/department_date', [JobController::class, 'getJobDepartmentByDate']);
    Route::patch('v1/job/updateStatus/{id}', [JobController::class, 'updateStatus']);
    Route::patch('v1/job/reverseStatus/{id}', [JobController::class, 'revertStatus']);
    Route::patch('v1/job/updateStatusBasedOnWork/{id}', [JobController::class, 'updateStatusBasedOnWork']);
    Route::put('v1/job/{id}', [JobController::class, 'update']); 
    Route::delete('v1/job/{id}', [JobController::class, 'deleteLogic']); 
    Route::patch('v1/job/restore/{id}', [JobController::class, 'restoreJob']);
    Route::post('v1/job/assignDepartment', [JobController::class, 'assignDepartment']);
    Route::delete('v1/job/remove_department/{jobId}/{departmentId}', [JobController::class, 'removeDepartmentFromJob']);

    //Rutas para notificaciones
    Route::get('v1/notificationAll', [NotificationController::class, 'get']);
    Route::get('v1/notificationUnRead', [NotificationController::class, 'getUnRead']);
    Route::get('v1/notificationRead', [NotificationController::class, 'getRead']);
    Route::patch('v1/notificacion/markAsRead/{notificationId}', [NotificationController::class, 'markAsRead']);
    Route::delete('v1/notificacion/deleteNotification', [NotificationController::class, 'deleteNotification']);

    //Rutas para workOrders
    Route::post('v1/workOrders', [WorkOrdersController::class, 'create']); 
    Route::post('v1/workOrdersComplete', [WorkOrdersController::class, 'createCompleteWorkOrder']); 
    Route::get('v1/workOrders', [WorkOrdersController::class, 'get']); 
    Route::get('v1/workOrders/{id}', [WorkOrdersController::class, 'getByID']); 
    Route::get('v1/workOrdersData/{id}', [WorkOrdersController::class, 'getWorkOrderById']); 
    Route::get('v1/workOrdersComplete/{id}', [WorkOrdersController::class, 'getCompleteWorkOrder']); 
    Route::get('v1/workOrdersCompleteByJob/{id}', [WorkOrdersController::class, 'getCompleteWorkByIDJob']); 
    Route::patch('v1/updateStatusWorkOrder', [WorkOrdersController::class, 'updateStatusWorkOrder']); 
    Route::get('v1/workOrdersByDepartment', [WorkOrdersController::class, 'getWorkOrderDepartmentByStatus']); 
    Route::get('v1/orderCompleteByStatus', [WorkOrdersController::class, 'getOrderCompleteByStatus']); 
    Route::put('v1/workOrders/{id}', [WorkOrdersController::class, 'update']);
    Route::patch('v1/workOrdersUpdate/{id}', [WorkOrdersController::class, 'updateOrdersWithEmployee']);
    Route::patch('v1/workOrdersFinish', [WorkOrdersController::class, 'finishWorkOrder']);
    Route::patch('v1/workOrdersFinish/edit', [WorkOrdersController::class, 'editFinishWorkOrder']);
    Route::patch('v1/workOrders/deleteImage', [WorkOrdersController::class, 'deleteImage']);
    Route::delete('v1/workOrders/{id}', [WorkOrdersController::class, 'delete']); 



    //Tabla Intermedia (assigned_worker)
    Route::post('v1/asignar_trabajador', [AssignedWorkerController::class, 'AssignWorker']);
    Route::get('v1/asignar_trabajador', [AssignedWorkerController::class, 'getWorkUserDate']);
    Route::get('v1/workOrderbyUser', [AssignedWorkerController::class, 'getWorkOrderJobUser']);
    Route::delete('v1/asignar_trabajador/{userId}/{workOrderId}', [AssignedWorkerController::class, 'detachWorkOrderFromUser']);

    //Tabla intermedia de MaterialAssigned
    Route::post('v1/materialAssigned', [MaterialAssignedController::class, 'AssignMaterial']);
    Route::post('v1/createAssignedMaterial', [MaterialAssignedController::class, 'createAssignedMaterial']);
    Route::patch('v1/assignMaterialEdit', [MaterialAssignedController::class, 'AssignMaterialEdit']);
    Route::delete('v1/deletedAssignedMaterial/{id}', [MaterialAssignedController::class, 'deletedAssignedMaterial']);
    Route::get('v1/materialAssigned/undeliveredOrders', [MaterialAssignedController::class, 'undeliveredOrdersMaterial']);
    Route::get('v1/materialAssigned/deliveredOrders', [MaterialAssignedController::class, 'deliveredOrdersMaterial']);
    Route::get('v1/materialAssigned/{id}', [MaterialAssignedController::class, 'getMaterialAssignedById']);
    Route::patch('v1/materialAssigned/accept_orders/{id}', [MaterialAssignedController::class, 'acceptOrders']);

    //Tabla intermedia equipos EquipmentAssigned
    Route::post('v1/quipmentAssigned', [EquipmentAssignedController::class, 'EquipmentAssign']);
    Route::post('v1/createEquipmentAssigned', [EquipmentAssignedController::class, 'createEquipmentAssigned']);
    Route::delete('v1/equipmentAssignedDelete/{id}', [EquipmentAssignedController::class, 'equipmentAssignedDelete']);
    Route::get('v1/quipmentAssigned/undeliveredOrders', [EquipmentAssignedController::class, 'undeliveredOrdersEquipment']);
    Route::get('v1/quipmentAssigned/deliveredOrders', [EquipmentAssignedController::class, 'deliveredOrdersEquipment']);
    Route::get('v1/quipmentAssigned/{id}', [EquipmentAssignedController::class, 'getEquipmentAssignedbyId']);
    Route::patch('v1/quipmentAssigned/{id}', [EquipmentAssignedController::class, 'acceptOrder']);
    Route::get('v1/equipmentEviction', [EquipmentAssignedController::class, 'getEquipmentEviction']);
    Route::patch('v1/completedEvictionEquipment/{id}', [EquipmentAssignedController::class, 'completedEvictionEquipment']);

    //ReportMaterial
    Route::post('v1/reportMaterial', [ReportMaterialController::class, 'create']);
    Route::get('v1/reportMaterialByEviction', [ReportMaterialController::class, 'getReportMaterialByEviction']);
    Route::patch('v1/completedReportMaterial/{id}', [ReportMaterialController::class, 'completedReportMaterial']);

    //CONTROLADOR PARA LOS REPORTES
    Route::get('v1/report/countJobByStatus', [ReportController::class, 'countJobByStatus']);
    Route::get('v1/report/countJobByEstablishment', [ReportController::class, 'countJobByEstablishment']);
    Route::get('v1/report/countJob', [ReportController::class, 'countJob']);
    Route::get('v1/report/totalmaterialValue', [ReportController::class, 'totalmaterialValue']);
    Route::get('v1/report/totalEquipmentValue', [ReportController::class, 'totalEquipmentValue']);

    //Controladores para magnitudes
    Route::post('v1/magnitude', [MagnitudeController::class, 'create']);
    Route::get('v1/magnitude', [MagnitudeController::class, 'get']);
    Route::put('v1/magnitude/{id}', [MagnitudeController::class, 'update']);
    Route::delete('v1/magnitude/{id}', [MagnitudeController::class, 'delete']);

    //Controladores para magnitude_values
    Route::post('v1/magnitude_value', [MagnitudeValueController::class, 'create']);
    Route::get('v1/magnitude_value', [MagnitudeValueController::class, 'get']);
    Route::get('v1/formatMagnitude', [MagnitudeValueController::class, 'getFormatMagnitude']);
    Route::put('v1/magnitude_value/{id}', [MagnitudeValueController::class, 'update']);
    Route::delete('v1/magnitude_value/{id}', [MagnitudeValueController::class, 'delete']);

    //Controladores para WorkOrderStatusesController
    Route::get('v1/WorkOrderStatuses/{id}', [WorkOrderStatusesController::class, 'getWorkOrderState']);
});



//----------------------------------------   RUTAS NO PROTEGIDAS ---------------------------------------------
//DEFINO LAS RUTAS PARA DEPARTAMENTO
Route::prefix('v1/department')->group(
    function(){
        Route::get('/', [DepartmentController::class, 'get']);
        Route::get('/with_out_admin', [DepartmentController::class, 'getDepartmentWithOutAdmin']);
        Route::get('/{id}', [DepartmentController::class, 'getById']);
    }
);

//DEFINO LAS RUTAS PARA USUARIO
Route::prefix('v1/user')->group(

    function(){
        Route::post('/', [UserController::class, 'create']);
        Route::post('/login', [UserController::class, 'login']);
        Route::post('/forgotPassword', [UserController::class, 'forgotPassword']);
        Route::get('/validatePassToken', [UserController::class, 'verifyPasswordResetToken']);
        Route::post('/updatePassword', [UserController::class, 'updatePassword']);
        
    }
);

//RUTAS PARA ROL
Route::prefix('v1/rol')->group(
    function(){
        Route::get('/',[RolController::class, 'get']);
    }
);



