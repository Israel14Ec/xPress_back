<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\job;
use App\Models\department;
use App\Models\job_status;
use App\Models\work_orders;
use App\Models\assigned_worker;
use App\Http\Controllers\UserController;
use App\Http\Requests\StoreJob;
use App\Http\Requests\StoreDepartmentAssigned;
use App\Events\UserAssigned;
use App\Notifications\newJob;
use Carbon\Carbon;

class JobController extends Controller
{
    //Crear
    public function create (StoreJob $request) {
        try {
            $job = Job::create($request->all());
            
            if($job) {
                return response()->json (['msg' => 'Se agrego correctamente el trabajo', 'data' => $job], 201); 
            }
            return response()->json(['msg' => 'No se pudo agregar al trabajo, intentelo de nuevo'], 404);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo crear'], 500);
        }
    }

     //Trabajos no borrados por el ID,
     public function getById($id) {
        try {
            $job = Job::find($id);

            if(!$job) {
                return response()->json(['msg' => 'Trabajo no encontrado'], 404);
            }
            
            return response()->json($job, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo obtener los datos'], 500);
        }
    }

    //Obtiene todos los trabajos no borrados
    public function getJobById($id, Request $request) {
        try {
            $idDepartment = $request->input('id_department');
    
            $job = Job::query()
                ->with(['departments' => function($query) use ($idDepartment) {
                    $query->where('departments.id_department', '!=', $idDepartment)
                        ->whereHas('users', function($query) {
                            $query->where('id_rol', 2);
                        })->with(['users' => function($query) {
                            $query->where('id_rol', 2);
                        }]);
                }])
                ->join('clients', 'jobs.id_client', '=', 'clients.id_client')
                ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
                ->join('job_priorities', 'jobs.id_job_priority', '=', 'job_priorities.id_job_priority')
                ->join('type_maintenances', 'jobs.id_type_maintenance', '=', 'type_maintenances.id_type_maintenance')
                ->join('communication_types', 'jobs.id_communication_type', '=', 'communication_types.id_communication_type')
                ->where('jobs.id_job', $id) 
                ->select(
                    'jobs.id_job',
                    'jobs.name_job',
                    'jobs.description',
                    'jobs.num_caf',
                    'jobs.start_date',
                    'jobs.before_picture',
                    'clients.name_client', 
                    'establishments.name_establishment',
                    'job_priorities.name as name_priority',
                    'job_priorities.level as level_priority',
                    'type_maintenances.name as name_maintenance',
                    'communication_types.name_communication'
                )
                ->first(); 
            
            if(!$job) {
                return response()->json(['msg' => 'Trabajo no encontrado'], 404);
            }
    
            return response()->json($job, 200);
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal no se pudo obtener los datos'], 500);
        }
    }
        
    
     //Obtiene los trabajos no eliminados en función de la fecha y estado de trabajo
     public function getByDate(Request $request) {
        try {
            $date = $request->input('date'); 
             // Convertir la fecha al formato correcto usando Carbon
            $dateFormatted = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        
            $id = $request->input('id_job_status');

            //Joins de la tabla
            $query = Job::query()
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
                            'job_priorities.name as name_priority',
                            'type_maintenances.name as name_maintenance',
                            'communication_types.name_communication'
                        ); 
            
            // Filtra por fecha si se proporciona
            if ($dateFormatted) {
                $query->whereDate('jobs.start_date', $dateFormatted);
            }
    
            // Filtra por id de estado de trabajo si se proporciona
            if ($id) {
                $query->where('jobs.id_job_status', $id);
            }
            
            $jobs = $query->get();
            return response()->json($jobs, 200);
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo obtener los datos'], 500);
        }
    }

   
    //Obtiene los trabajos no eliminados en función de la fecha o estado de trabajo, con paginación
    public function getJobByStatusOrDate(Request $request) {
        try {
            $date = $request->input('date'); 
            $id = $request->input('id_job_status');
            $perPage = intval($request->input('per_page'));
            $currentPage = intval($request->input('page'));
            
            $dateFormatted = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');

            // Incluye la relación workOrders en la consulta
            $query = Job::with(['workOrders' => function ($query) {
                $query->select('id_work_order', 'id_job');  
            }])
                        ->join('clients', 'jobs.id_client', '=', 'clients.id_client')
                        ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
                        ->join('job_priorities', 'jobs.id_job_priority', '=', 'job_priorities.id_job_priority')
                        ->join('type_maintenances', 'jobs.id_type_maintenance', '=', 'type_maintenances.id_type_maintenance')
                        ->join('communication_types', 'jobs.id_communication_type', '=', 'communication_types.id_communication_type')
                        ->join('job_statuses', 'jobs.id_job_status', '=', 'job_statuses.id_job_status')
                        ->select(
                            'jobs.id_job',
                            'jobs.name_job',
                            'jobs.description',
                            'jobs.num_caf',
                            'jobs.start_date',
                            'jobs.before_picture',
                            'clients.name_client', 
                            'establishments.name_establishment',
                            'job_priorities.name as name_priority',
                            'type_maintenances.name as name_maintenance',
                            'communication_types.name_communication',
                            'job_statuses.name as name_status',
                            'job_statuses.color'
                        ); 
            
            // Filtra por fecha si se proporciona
            if ($date) {
                $query->whereDate('jobs.start_date', $dateFormatted);
            }
    
            // Filtra por id de estado de trabajo si se proporciona
            if ($id) {
                $query->where('jobs.id_job_status', $id);
            }
            
            // Ordena los resultados por fecha de inicio en orden descendente
            $query->orderBy('jobs.start_date', 'desc');

            $jobs = $query->paginate($perPage, ['*'], 'page', $currentPage);
            return response()->json($jobs, 200);
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo obtener los datos'], 500);
        }
    }
    

    //Obtiene los trabajos eliminados logicamente
    public function getDelete() {
        try {
            $job = Job::onlyTrashed()->get(); 
            return response()->json($job, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo obtener los datos'], 500);
        }
    }


    //Actualizar
    public function update ($id, Request $request) {

        try {
            $job = Job::find($id);
            if (!$job) {
                return response()->json(['msg' => 'No se encontró al trabajo'], 404);
            }
            $job->update($request->all());
            return response()->json(['msg' => 'Se edito los datos del trabajo correctamente', 'data' => $job], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo actualizar los datos'], 500);
        }
    }

    //Actualizar estado de trabajo

    public function updateStatus ($id) {
        try {

            $job = Job::find($id);
            if(!$job) {
                return response()->json(['msg' => 'No se encontró al trabajo'], 404);
            }
            $currentStatus = job_status::find($job->id_job_status); //Estado actual del trabajo
            $nextStatus = job_status::where('step', $currentStatus->step + 1)->first();

            if ($nextStatus) {
                $job->id_job_status = $nextStatus->id_job_status;
                $job->save();
                return response()->json(['msg' => 'El estado del trabajo se actualizo a '. $nextStatus->name, 'data' => $job]);
            } 

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo actualizar el estado del trabajo'], 500);
        }
    }

    // Regresar al estado anterior de trabajo
    public function revertStatus($id) {
        try {
            $job = Job::find($id);
            if (!$job) {
                return response()->json(['msg' => 'No se encontró el trabajo'], 404);
            }
            $currentStatus = job_status::find($job->id_job_status); // Estado actual del trabajo

            // Verificar si hay un estado anterior
            $previousStatus = job_status::where('step', '<', $currentStatus->step)->orderBy('step', 'desc')->first();

            if ($previousStatus) {
                $job->id_job_status = $previousStatus->id_job_status;
                $job->save();
                return response()->json(['msg' => 'El estado del trabajo se ha revertido a '. $previousStatus->name, 'data' => $job]);
            } else {
                return response()->json(['msg' => 'Ya está en el estado inicial o no hay estado previo'], 404);
            }

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo revertir el estado del trabajo'], 500);
        }
    }

    //Actualizar el estado de trabajo tomando en cuenta el work_order_statuses
    public function updateStatusBasedOnWork ($jobId) {
        try {
            
            $job = Job::findOrFail($jobId); 

            $workOrders = work_orders::where('id_job', '=', $jobId)->get(); //Orden de trabajo
            $countWorkOrder = $workOrders->count(); //Obtiene la cantidad de ordenes de trabajo existente
            
            $countDepartmentsAssigned = $job->departments()->count(); //Cantidad de departamentos asignados al trabajo
            
            //Mapear los estados con el step
            $jobStepMapping = [
                1 => 3,
                2 => 4,
                3 => 5
            ];

            if($countWorkOrder != $countDepartmentsAssigned) {
                return response()->json(['msg' => 'No se puede actualizar el estado del trabajo, por que aun no estan todas las ordenes de trabajo listas'], 400);
            } 
            
            // Obtener el menor `step` de las órdenes de trabajo
            $stepMin = $workOrders->min('id_order_statuses');
            $mappedStep = $jobStepMapping[$stepMin] ?? 5;
            $jobIdStatus = job_status::where('step', '=', $mappedStep)->value('id_job_status');
            
            //Actualizar el estado del trabajo
            $job->id_job_status = $jobIdStatus;
            $job->save();
            
            return response()->json(['msg' => 'Se actualizo el estado de trabajo', 
            'data' => $job], 202);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage());
        }
    }

    //FUNCIÓN ESTÁTICA PARA Actualizar el estado de trabajo tomando en cuenta el work_order_statuses
    public static function updateStatusBasedOnWorkStatic ($jobId) {
        try {
            $job = Job::findOrFail($jobId);

            $workOrders = work_orders::where('id_job', '=', $jobId)->get(); // Orden de trabajo
            $countWorkOrder = $workOrders->count(); // Obtiene la cantidad de órdenes de trabajo existentes

            $countDepartmentsAssigned = $job->departments()->count(); // Cantidad de departamentos asignados al trabajo

            // Mapear los estados con el step
            $jobStepMapping = [
                1 => 3,
                2 => 4,
                3 => 5
            ];

            if ($countWorkOrder != $countDepartmentsAssigned) {
                return response()->json(['msg' => 'No se puede actualizar el estado del trabajo, porque aún no están todas las órdenes de trabajo listas'], 400);
            }

            // Obtener el menor `step` de las órdenes de trabajo
            $stepMin = $workOrders->min('id_order_statuses');
            $mappedStep = $jobStepMapping[$stepMin] ?? 5;
            $jobIdStatus = job_status::where('step', '=', $mappedStep)->value('id_job_status');

            // Actualizar el estado del trabajo
            $job->id_job_status = $jobIdStatus;
            $job->save();

            return response()->json(['msg' => 'Se actualizó el estado de trabajo', 'data' => $job], 202);

        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    //Eliminado Logico
    public function deleteLogic ($id) {
        try {
            $job = Job::find($id);
            if (!$job) {
                return response()->json(['msg' => 'El trabajo no existe'], 404);
            }
            $job->delete();
            return response()->json(['msg' => 'El trabajo fue borrado', 'data' => $job], 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo eliminar los datos'], 500);
        }
    }

    //Restaurar
    public function restoreJob($id){
        try {
            $job = Job::withTrashed()->find($id);
            if (!$job) {
                return response()->json(['msg' => 'El trabajo no existe o ya está activo'], 404);
            }
            $job->restore(); 
            return response()->json(['msg' => 'El trabajo fue restaurado exitosamente', 'data' => $job], 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo restaurar los datos'], 500);
        }
    }


    // --------- USO DE LA TABLA INTERMEDIA ----------------------------------------------------

    //Asignar trabajo a departamento y notificar
    public function assignDepartment(StoreDepartmentAssigned $request) {
        try {

            $jobId = $request->input('id_job');
            $departmentIds = $request->input('id_departments'); 
            $departmentAssigned = Job::findOrFail($jobId); //Valida que exista un Job
            $subject = "Nuevo trabajo asignado";

            foreach ($departmentIds as $departmentId) {


                $departmentAssigned->departments()->attach($departmentId, ['created_at' => now(), 'updated_at' => now()]);
                $departmentHeads = UserController::getDepartmentHeads($departmentId); //Obtiene los usuarios
                
                foreach ($departmentHeads as $departmentHead) {
                    
                    $notification = new newJob(
                        $jobId, 
                        $departmentHead->id_user, 
                        $subject, //Asunto 
                        $departmentAssigned->name_job, 
                        $departmentAssigned->description,
                        $departmentAssigned->start_date
                    ); //Instancia de notificacion

                    $departmentHead->notify($notification); //Notificar

                    $notificationId = $departmentHead->notifications()->latest()->first()->id; //Id de la ultima notificacion
                     // Preparar los datos para el evento
                    $eventData = [
                        'id' => $notificationId,
                        'id_job' => $jobId,
                        'subject' => $subject,
                        'to_user' => $departmentHead->id_user,
                        'title' => $departmentAssigned->name_job,
                        'description' => $departmentAssigned->description,
                        'date' => $departmentAssigned->start_date
                    ];

                    event(new \App\Events\UserAssigned($eventData, $departmentHead->id_user)); //Emitir el evento
                }
            }
            return response()->json(['msg' => 'El trabajo fue asignado correctamente', 'data' => $departmentAssigned], 201);
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salio mal no se pudo asignar un departamento'], 500);
        }
    }
    
    

    //Obtener los departamentos en función del trabajo
    public function getDepartmentsAssignedToJob($jobId)
    
    {
        try {
            $job = Job::with('departments')->findOrFail($jobId);

            return response()->json($job, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudieron obtener los departamentos asociados al trabajo'], 500);
        }
    }

    //Obtener los trabajos x departamentos asociados segun la fecha y estado de trabajo
    public function getJobDepartmentByDate(Request $request) {
        try {
            $date = $request->input('date');
            $departmentId = $request->input('id_department');
            $idStatus = $request->input('id_job_status');
            
            if (!$date) {
                return response()->json(['msg' => 'Se debe ingresar una fecha'], 400);
            }
    
            // Obtener trabajos por fecha y estado
            $jobs = Job::query()
                ->join('clients', 'jobs.id_client', '=', 'clients.id_client')
                ->join('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
                ->join('job_priorities', 'jobs.id_job_priority', '=', 'job_priorities.id_job_priority')
                ->join('type_maintenances', 'jobs.id_type_maintenance', '=', 'type_maintenances.id_type_maintenance')
                ->join('communication_types', 'jobs.id_communication_type', '=', 'communication_types.id_communication_type')
                ->whereDate('jobs.start_date', '=', $date)
                ->where('id_job_status', '=', $idStatus)
                ->whereHas('departments', function ($query) use ($departmentId) {
                    $query->where('departments.id_department', $departmentId);
                })
                ->select(
                    'jobs.id_job',
                    'jobs.name_job',
                    'jobs.description',
                    'jobs.num_caf',
                    'jobs.start_date',
                    'jobs.before_picture',
                    'jobs.start_date',
                    'clients.name_client', 
                    'establishments.name_establishment',
                    'job_priorities.name as name_priority',
                    'type_maintenances.name as name_maintenance',
                    'communication_types.name_communication'
                )
                ->with(['departments' => function ($query) use ($departmentId) {
                    $query->where('departments.id_department', $departmentId);
                }])
                ->get();
    
            // Verificar si cada trabajo tiene órdenes de trabajo para el departamento especificado
            $jobs->each(function ($job) use ($departmentId) {
                $job->has_work_order = $job->workOrders()
                    ->whereHas('workers', function ($query) use ($departmentId) {
                        $query->where('users.id_department', $departmentId);
                    })
                    ->exists();
            });
    
            // Filtrar trabajos para retornar un JSON vacío si todos tienen has_work_order = true
            $jobs = $jobs->filter(function ($job) {
                return !$job->has_work_order;
            });
    
            // Si todos los trabajos tienen has_work_order = true, retornar JSON vacío
            if ($jobs->isEmpty()) {
                return response()->json([]);
            }
    
            return response()->json($jobs, 200);
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo obtener los datos del trabajo y sus departamentos'], 500);
        }
    }
        
    
    //Eliminar trabajo y departamento
    public function removeDepartmentFromJob ($jobId, $departmentId) {
        try {
            // Encuentra el trabajo específico
            $job = Job::findOrFail($jobId);

            // Elimina la asociación con el departamento
            $job->departments()->detach($departmentId);

        return response()->json(['message' => 'Departamento eliminado del trabajo con éxito.']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo eliminar'], 500);
        }
    }

}
