<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\job;
use App\Models\material;
use App\Models\construction_equipment;

class ReportController extends Controller
{   
    //Cantidad de trabajos en función de su estado
    public function countJobByStatus()
    {
        try {
            $jobCounts = Job::selectRaw('COALESCE(COUNT(jobs.id_job), 0) AS total_value, job_statuses.name')
                ->rightJoin('job_statuses', 'jobs.id_job_status', '=', 'job_statuses.id_job_status')
                ->groupBy('job_statuses.name')
                ->get();
    
            return response()->json($jobCounts, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo obtener los datos'], 500);
        }
    }

    //Cantidad de trabajos completados en función del establecimiento 
    public function countJobByEstablishment() {
        try {
            $jobCounts = Job::rightJoin('establishments', 'jobs.id_establishment', '=', 'establishments.id_establishment')
            ->where('jobs.id_job_status', '=', 6)
            ->selectRaw('COALESCE(COUNT(jobs.id_job), 0) AS total_value, establishments.name_establishment')
            ->groupBy('establishments.name_establishment')
            ->get();
            return response()->json($jobCounts, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo obtener los datos'], 500);
        }
    }

    //Cantidad de trabajos completados
    public function countJob(){

        try {
            $jobCount = Job::where('id_job_status', 6)->count();
            return response()->json($jobCount, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo obtener los datos'], 500);
        }
    }

    //Valor del inventario del material
    public function totalmaterialValue() {
        try {
            $materialValue = Material::sum('total_value');
            return response()->json($materialValue, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo obtener los datos'], 500);
        }
    }

    //Valor del inventario del equipo
    public function totalEquipmentValue () {
        try {
            $equipmentValue = construction_equipment::sum('unit_value');
            return response()->json($equipmentValue, 200);

        } catch (\Throwable $th) {
             return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo obtener los datos'], 500);
        }
    }
    
}
