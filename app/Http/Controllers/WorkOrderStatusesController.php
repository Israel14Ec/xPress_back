<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\work_order_statuses;

class WorkOrderStatusesController extends Controller
{
    //Obtener el id del estado de la orden de trabajo en función del step

    public function getWorkOrderState ($step) {
        try {
            $status = work_order_statuses::where('step', '=', $step)->get();
            return response()->json($status);
            
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage());
        }
    }

}
