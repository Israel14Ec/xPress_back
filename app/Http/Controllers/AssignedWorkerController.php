<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\work_orders;
use App\Models\job;
use App\Models\user;
use App\Models\assigned_worker;
use App\Http\Requests\StoreAssignedWorker;
use App\Notifications\NewWorkOrder;

class AssignedWorkerController extends Controller
{

    //Asignar a varios trabajadores una orden de trabajo y notificar 
    public static function AssignWorker($userIds, $workOrderId) {
        try {
            
            $assignedUsers = []; 
            $workOrder = work_orders::findOrFail($workOrderId); 
            $job = Job::findOrFail($workOrder->id_job); 
            $subject = "Nueva orden de trabajo asignada";

            foreach ($userIds as $userId) {

                $user = User::findOrFail($userId); 
                $workOrder->workers()->attach($user->id_user);
                
                //Notificar
                $notification = new newWorkOrder(
                    $workOrderId,
                    $userId,
                    $subject,
                    $job->name_job,
                    $job->description,
                    $workOrder->instructions,
                    $workOrder->assigned_date
                );

                $user->notify($notification); //Notificar
                $notificationId = $user->notifications()->latest()->first()->id; //Ultimo ID

                $eventData = [
                    'id' => $notificationId,
                    'id_work_order' => $workOrderId,
                    'to_user' => $userId,
                    'subject' => $subject,
                    'title' => $job->name_job,
                    'description' => $job->description,
                    'instructions' => $workOrder->instructions,
                    'date' => $workOrder->assigned_date
                ];

                event(new \App\Events\WorkAssigned($eventData, $user->id_user)); //Emitir el evento
                $assignedUsers[] = $user;
            }
            
            return $assignedUsers;
    
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    

   //Obtener la orden de trabajo en función del usuario, de la tabla intermedia, en función del id_order_statuses
   public function getWorkUserDate(Request $request) {
    try {
        $userId = $request->input('id_user'); 
        $orderStatusId = $request->input('id_order_statuses');

        $assignedWorkOrders = User::findOrFail($userId)
        ->assignedWorkOrders()
        ->where('id_order_statuses', $orderStatusId)
        ->with([
            'job' => function($query) {
                $query->with('establishment'); // Asumiendo que hay una relación 'establishment' en el modelo 'Job'
            }, 
            'materialAssigned' => function($query) {
                $query->withPivot('amount');
            }, 
            'equipmentAssigned', 
            'workers' => function($query) {
                $query->select('users.id_user', 'users.name', 'users.last_name', 'users.phone_number', 'users.email'); 
            }
        ])
            ->get()
            ->each(function($workOrder) {
                $workOrder->materialAssigned->each(function($material) {
                    $material->amount = $material->pivot->amount;
                });
            });
            
            return response()->json($assignedWorkOrders, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo obtener los datos de la orden de trabajo'], 500);
        }
    }

    //Obtiene el work_order y job en función del usuario
    public function getWorkOrderJobUser (Request $request) {
        try {
            $userId = $request->input('id_user'); 
            $jobStatusId = $request->input('id_job_status');

            $workOrder = User::findOrFail($userId)
            ->assignedWorkOrders()
            ->whereHas('job', function($query) use ($jobStatusId) {
                $query->where('id_job_status', $jobStatusId);
            })
            ->with('job')->get();

            return response()->json($workOrder, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo obtener los datos de la orden de trabajo'], 500);
        }
    }
    
    //Eliminar la asociación de la tabla intermedia
    public function detachWorkOrderFromUser($userId, $workOrderId) {
        try {
    
            $user = User::findOrFail($userId);
            $user->assignedWorkOrders()->detach($workOrderId);
    
            return response()->json(['msg' => 'Se ha elimido al trabajador de la orden de trabajo', 
                'data'=>$user], 200);
        
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo desasociar la orden de trabajo del usuario'], 500);
        }
    } 
}
