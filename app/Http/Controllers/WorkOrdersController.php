<?php

namespace App\Http\Controllers;

use App\Models\work_orders;
use App\Models\job;
use App\Models\user;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCompleteWorkOrder;
use App\Http\Requests\StoreEditWorkOrder;
Use App\Http\Controllers\AssignedWorkerController;
Use App\Http\Controllers\MaterialAssignedController;
Use App\Http\Controllers\EquipmentAssignedController;
Use App\Http\Controllers\UserController;
Use App\Http\Controllers\ConstructionEquipmentController;
Use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\JobController;
use App\Notifications\reportWorkOrderComplete;
use App\Notifications\NewWorkOrder;


class WorkOrdersController extends Controller
{
    
    // Crear una orden de trabajo
    public function create($idJob, $instructions, $assignedDate, $endDate) {

        $idOrderStatuses = 1; //Estado de la orden de trabajo (asignado recursos)

        $workOrderData = [
            'id_job' => $idJob,
            'instructions' => $instructions,
            'assigned_date' => $assignedDate,
            'end_date' => $endDate,
            'id_order_statuses' => $idOrderStatuses
            
        ];

        $orders = work_orders::create($workOrderData);
        if(!$orders) {
            throw new \Exception('No se pudo guardar la orden de trabajo');
        }
        return $orders;
    }




    //Obtener todos los WorkOrders 
    public function get () {
        try {
            $orders = work_orders::get();
            return response()->json($orders, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(),'msg' => 'No se pudo obtener los datos'], 500);
        }
    }

    //Obtiene el workOrder x ID, junto con los datos de Job
    public function getByID ($id) {
        try {

            $orders = work_orders::with('job')->find($id);
            if(!$orders) {
                return response()->json (['msg' => 'No se encontraron los registros'], 404);
            }
            return response()->json($orders, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(),'msg' => 'No se pudo obtener los datos'], 500);
        }
    }

    //Obtiene el workOrder x ID
    public function getWorkOrderById ($id) {
        try {
            $orders = work_orders::with('job')
                    ->with(['workers' => function($query) {
                        $query->select(
                            'users.id_user', 
                            'users.name', 
                            'users.last_name', 
                            'users.phone_number', 
                            'users.image', 
                            'users.email', 
                            'users.id_department', 
                            'users.id_rol');
                    }])
                    ->with(['materialAssigned' => function($query) {
                        $query->withPivot('id_material_assigned' 
                        ,'amount', 'delivered_amount', 'is_delivered');
                    }])
                    ->with(['equipmentAssigned' => function($query) {
                        $query->withPivot('id_equipment_assigned', 'is_delivered');
                    }])
                    ->findOrFail($id);
    
            return response()->json($orders);
    
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(), 
                'msg' => 'No se encontró los datos de la ordén de trabajo'], 404);
        }
    }
    

    //Actualiza el estado de workOrder al siguiente estado 
    public function updateStatusWorkOrder(Request $request) {
        try {
           
            $idWorkOrder = $request->input('id_work_order');
            $idStatus = $request->input('id_order_statuses');

            $order = work_orders::findOrFail($idWorkOrder);
    
            if ($order) {
                $order->id_order_statuses = $idStatus;
                $order->save();
    
                // Obtener el nombre del nuevo estado
                $newStatus = $order->workOrderStatus()->first();

                // Llamar al método estático updateStatusBasedOnWork del controlador Job
                JobController::updateStatusBasedOnWorkStatic($order->id_job);
    
                return response()->json([
                    'data' => $order,
                    'msg' => 'Se actualizó el estado de la orden de trabajo a ' . $newStatus->name
                ], 200);
            }
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo actualizar el estado de trabajo'], 500);
        }
    }
    

    //Obtiene todos los datos de workOrder x Job, en funcion de id_department y id_job_status 
    public function getWorkOrderDepartmentByStatus(Request $request) {
        try {
            $departmentId = $request->input('id_department');
            $jobStatusId = $request->input('id_job_status');
    
            $workOrders = work_orders::whereHas('job', function($query) use ($departmentId, $jobStatusId) {
                $query->where('id_job_status', $jobStatusId)
                      ->whereHas('departments', function($subQuery) use ($departmentId) {
                          $subQuery->where('departments.id_department', $departmentId);
                      });
            })
            ->with('job')
            ->get();
    
            return response()->json($workOrders, 200);
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo obtener los datos del trabajo'], 500);
        }
    }

    //Obtiene todos los datos de workOrder x Job en función del id_department y id_order_statuses
    public function getOrderCompleteByStatus(Request $request)
    {
        try {
            $departmentId = $request->input('id_department');
            $orderStatusId = $request->input('id_order_statuses');

            $workOrders = work_orders::where('id_order_statuses', $orderStatusId)
                ->whereHas('workers', function ($query) use ($departmentId) {
                    $query->where('id_department', $departmentId);
                })
                ->with('job') 
                ->get();

            return response()->json($workOrders, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo obtener los datos del trabajo'], 500);
        }
    }


    //Actualizar la orden 
    public function update($id, Request $request) {
        try {
            $order = work_orders::find($id);
            if(!$order) {
                return response()->json(['msg' => 'La orden de trabajo no se encontró'], 404);
            }
            
            $updatedData = $request->all();
            
            if ($request->has('after_picture')) {
                $newImage = $request->input('after_picture');
        
                $images = $order->after_picture ?: [];
                
                if ($newImage) {
                    $images[] = $newImage;
                }
        
                $updatedData['after_picture'] = $images;
            }
            
            $order->update($updatedData);
            
            return response()->json(['msg' => 'Se editaron los datos correctamente', 'data' => $order], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo actualizar los datos'], 500);
        }
    }

    //Actualiza la ordén de trabajo junto con los empleados asignados
    public function updateOrdersWithEmployee(StoreEditWorkOrder $request, $idWorkOrder) {
        try {
            \DB::beginTransaction();
            $order = work_orders::with('workers')->findOrFail($idWorkOrder);
            $instructions = $request->input('instructions');
            $assignedDate = $request->input('assigned_date');
            $endDate = $request->input('end_date');
            $newUserIds = $request->input('id_users');
            $currentUsers = $order->workers->pluck('id_user')->toArray();
            
            // Actualizar los detalles de la orden de trabajo
            $order->instructions = $instructions;
            $order->assigned_date = $assignedDate;
            $order->end_date = $endDate;
            $order->save();

            // Usuarios para agregar y notificar
            $usersToAdd = array_diff($newUserIds, $currentUsers);
            // Usuarios para desasociar y notificar
            $usersToRemove = array_diff($currentUsers, $newUserIds);

            // Procesar nuevos usuarios
            foreach ($usersToAdd as $userId) {
                $order->workers()->attach($userId);
                $this->notifyUser($userId, $order, true);  
            }

            // Procesar usuarios a remover
            foreach ($usersToRemove as $userId) {
                $order->workers()->detach($userId);
                $this->notifyUser($userId, $order, false); 
            }

            $order->load('workers'); 

            \DB::commit();
            return response()->json(['msg' => 'Orden actualizada y notificaciones enviadas correctamente', 'data' => $order]);
        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    //notificación para la edición edición
    private function notifyUser($userId, $workOrder, $isAssigned) {

        $user = User::findOrFail($userId);
        $job = Job::findOrFail($workOrder->id_job);
        $subject = $isAssigned ? "Nueva orden de trabajo asignada" : 
        "La ordén de trabajo fue asignado a otra persona";

        // Notificación
        $notification = new newWorkOrder(
            $workOrder->id_work_order,
            $user->id_user,
            $subject,
            $job->name_job,
            $job->description,
            $workOrder->instructions,
            $workOrder->assigned_date
        );

        $user->notify($notification);
        $notificationId = $user->notifications()->latest()->first()->id; //Ultimo ID

        //Datos a enviar con el websocket
        $eventData = [
            'id' => $notificationId,
            'id_work_order' => $workOrder->id_work_order,
            'to_user' =>  $user->id_user,
            'subject' => $subject,
            'title' => $job->name_job,
            'description' => $job->description,
            'instructions' => $workOrder->instructions,
            'date' => $workOrder->assigned_date
        ];

        event(new \App\Events\WorkAssigned($eventData, $user->id_user)); //Emitir el evento
    }
    

    //Eliminar
    public function delete ($id) {
        try {
            $orders = work_orders::find($id);
            if(!$orders) {
                return response()->json(['msg' => 'El estado de trabajo no existe'], 404);
            }
            $orders->delete();
            return response()->json(['msg' => 'Se elimino exitosamente la ordén de trabajo' ,'error' => $orders], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo eliminar los datos'], 500);
        }
    }

    //Elimina la foto del vector
    public function deleteImage(Request $request) {
        try {
            
            $id = $request->input('id_work_order');
            $url = $request->input('url');

            $order = work_orders::find($id);
   
            if(!$order) {
                return response()->json(['msg' => 'No se encontró la orden de trabajo'], 404);
            }
            // Obtener el vector de imágenes de la orden de trabajo
            $images = $order->after_picture;
            // Buscar la posición de la imagen en el vector
            $index = array_search($url, $images);
            if ($index !== false) {
                // Eliminar la imagen del vector
                unset($images[$index]);
    
                // Actualizar el vector de imágenes en la orden de trabajo
                $order->after_picture = array_values($images);
                $order->save();
    
                return response()->json(['msg' => 'Imagen eliminada correctamente'], 200);
            } else {
                return response()->json(['msg' => 'La imagen no se encontró en la orden de trabajo'], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo eliminar la imagen'], 500);
        }
    }
    
    
    //--------------- TABLAS INTERMEDIAS ----------------------------------------------------

     //Crear una orden de Trabajo Completa con sus respectivas tablas intermedias:
     public function createCompleteWorkOrder (StoreCompleteWorkOrder $request) {

        \DB::beginTransaction();
        try {

            $idJob = $request->input('id_job');
            $instructions = $request->input('instructions');
            $assignedDate = $request->input('assigned_date');
            $endDate = $request->input('end_date');
            
            $userIds = $request->input('id_user'); //assigned_workers

            $materials = $request->input('materials'); //material_assigned

            $equipments = $request->input('id_equipment_assigned');//equipment_assigned

            //Crear orden de trabajo (work_orders)
            $workOrder = $this->create($idJob, $instructions, $assignedDate, $endDate);
            $workOderId = $workOrder->id_work_order;

            //assigned_workers
            $users =  AssignedWorkerController::AssignWorker($userIds, $workOderId);

            // material_assigned
            $assignedMaterials = null;
            if ($materials) {
                $assignedMaterials = MaterialAssignedController::AssignMaterial($materials, $workOderId);
            }
            
            // equipment_assigned
            $assignedEquipments = null;
            if ($equipments) {
                $assignedEquipments = EquipmentAssignedController::EquipmentAssign($equipments, $workOderId);
            }
            
            \DB::commit();
            return response()->json(['msg' => 'La orden de trabajo se creo', 
                'work_orders' => $workOrder, 
                'users' => $users,
                'assigned_materials' => $assignedMaterials,
                'assigned_equipments' => $assignedEquipments
            ], 201);

        } catch (\Throwable $th) {

            \DB::rollBack();
            return response()->json(['error' => $th->getMessage(), 'msg'=>'No se pudo crear la orden de trabajo, intentelo de nuevo'], 500);
        }
    }

    // Obtiene la orden de trabajo con sus tablas intermedias x ID
    public function getCompleteWorkOrder($idWorkOrder) {
        try {
            // Busca la orden de trabajo y sus relaciones asociadas
            $workOrder = work_orders::with([
                'workers', 
                'materialAssigned' => function($query) {
                    $query->withPivot('amount', 'delivered_amount', 'id_material_assigned'); // Incluye el campo amount de la tabla pivot
                }, 
                'equipmentAssigned', 
                'job'   => function($query) {
                    $query->with('establishment');
                },
            ])->findOrFail($idWorkOrder);

            $workOrder->materialAssigned->each(function($material) {
                $material->amount = $material->pivot->amount;
                $material->delivered_amount = $material->pivot->delivered_amount;
            });

            return response()->json($workOrder, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo obtener los datos de la orden de trabajo'], 500);
        }
    }

    //Obtiene los datos de la orden de trabajo pasandole el idJob
    public function getCompleteWorkByIDJob($idJob) {
        try {
            $job = Job::with([
                'workOrders' => function($query) {
                    $query->with([
                        'workers',
                        'materialAssigned' => function($query) {
                            $query->withPivot('amount', 'id_material_assigned', 'delivered_amount'); // Incluye los campos adicionales de la tabla pivot
                        },
                        'equipmentAssigned',
                    ]);
                }
            ])
            ->join('clients', 'jobs.id_client', '=', 'clients.id_client')
            ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
            ->join('job_priorities', 'jobs.id_job_priority', '=', 'job_priorities.id_job_priority')
            ->join('type_maintenances', 'jobs.id_type_maintenance', '=', 'type_maintenances.id_type_maintenance')
            ->join('communication_types', 'jobs.id_communication_type', '=', 'communication_types.id_communication_type')
            ->select(
                'jobs.id_job',
                'jobs.name_job',
                'jobs.description',
                'jobs.num_caf',
                'jobs.start_date',
                'jobs.before_picture',
                'clients.name_client', 
                'establishments.name_establishment',
                'establishments.description as description_establishment',
                'establishments.location',
                'job_priorities.name as name_priority',
                'type_maintenances.name as name_maintenance',
                'communication_types.name_communication'
            )
            ->find($idJob);
        
            if (!$job) {
                return response()->json(['msg' => 'Orden de trabajo no encontrada'], 404);
            }
    
            // Almacena los nombres de los departamentos en un array para evitar consultas repetitivas
            $departmentNames = [];
    
            foreach ($job->workOrders as $workOrder) {
                $workOrder->materialAssigned->each(function($material) {
                    $material->amount = $material->pivot->amount;
                    $material->id_material_assigned = $material->pivot->id_material_assigned;
                    $material->delivered_amount = $material->pivot->delivered_amount;
                });
    
                if ($workOrder->workers->isNotEmpty()) {
                    $idDepartment = $workOrder->workers->first()->id_department;
    
                    // Reutiliza el nombre del departamento si ya fue consultado
                    if (!isset($departmentNames[$idDepartment])) {
                        $departmentNames[$idDepartment] = DepartmentController::getNameDepartmentById($idDepartment);
                    }
    
                    // Asigna el nombre del departamento a la workOrder
                    $workOrder->department = $departmentNames[$idDepartment];
                }
            }
        
            return response()->json($job, 200);
        
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo obtener los datos de la orden de trabajo'], 500);
        }
    }
    
    
    
    //Finalizar tabla de WorkOrders
    public function finishWorkOrder (Request $request) {
        try {
            \DB::beginTransaction();
            $idStatus = 5; // El equipo esta en proceso de desalojo

            $workOrderId = $request->input('id_work_order');
            $hoursWorked = $request->input('hour_job');
            $newAfterPicture = $request->input('after_picture'); 
            $departmentId = $request->input('id_department');
            
            $workOrder = work_orders::findOrFail($workOrderId);
            $workOrder->hour_job = $hoursWorked;

            $currentDate = Carbon::now()->toDateString(); //Fecha final

            // Añade la nueva imagen al array existente
            if ($newAfterPicture) {
                $currentPictures = $workOrder->after_picture ?: [];
                $currentPictures[] = $newAfterPicture; 
                $workOrder->after_picture = $currentPictures; 
            }
            // Guarda los cambios en la base de datos
            $workOrder->save();
            $job = job::find($workOrder->id_job); //Obtengo el trabajo
            
            $departmentHeads = UserController::getDepartmentHeads($departmentId);

            foreach ($departmentHeads as $departmentHead) {
                //Notifica la finalización de la orden de trabajo
                $notification = new reportWorkOrderComplete(
                    $workOrderId,   
                    $departmentHead->id_user,
                    "La orden de trabajo: ".$job->name_job. "finalizo con éxito", //Subject
                    $job->name_job,
                    $job->description,
                    $currentDate
                );

                $departmentHead->notify($notification); //Notificar
                $notificationId = $departmentHead->notifications()->latest()->first()->id; //Id de la ultima notificacion

                //Emitir el evento:
                $eventData = [
                    'id' => $notificationId,
                    'to_user' => $departmentHead->id_user,
                    'subject'=>  "La orden de trabajo: ".$job->name_job. " finalizo con éxito",
                    'title' => $job->name_job,
                    'description' => $job->description,
                    'date' =>   $currentDate
                ];

                event(new \App\Events\ReportWorkComplete($eventData, $departmentHead->id_user)); //Emitir el evento    
            }

            $equipmentAssigned = $workOrder->equipmentAssigned()->get();
            if ($equipmentAssigned->isNotEmpty()) {
                foreach ($equipmentAssigned as $assigned) {
                    $equipmentId = $assigned->id_construction_equipment;
                    ConstructionEquipmentController::updateStatusEquipment($equipmentId, $idStatus);
                }
            }
         
            \DB::commit();
            return response()->json(['msg' => 'Se completo la orden de trabajo', 'data' => $workOrder], 200);
            
        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo finalizar la orden
            de trabajo'], 500);
        }
    }

   
    // Edita la tabla WorkOrders con las opciones de empleado (Foto y horas de trabajo)
    public function editFinishWorkOrder(Request $request) {
        try {
            \DB::beginTransaction();
            $workOrderId = $request->input('id_work_order');
            $hoursWorked = $request->input('hour_job');
            $newAfterPicture = $request->input('after_picture');
            
            $workOrder = work_orders::findOrFail($workOrderId);
            
            // Actualizar las horas trabajadas
            $workOrder->hour_job = $hoursWorked;

            // Añadir la nueva imagen al vector de imágenes existente
            if ($newAfterPicture) {
                $currentPictures = $workOrder->after_picture ?: [];
                $currentPictures[] = $newAfterPicture;
                $workOrder->after_picture = $currentPictures;
            }

            // Guardar los cambios en la base de datos
            $workOrder->save();

            \DB::commit();
            return response()->json(['msg' => 'Se edito correctamente los datos', 'data' => $workOrder], 200);
        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo editar la orden de trabajo, 
            intentelo de nuevo'], 500);
        }
    }


}
