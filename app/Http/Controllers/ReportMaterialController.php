<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\reportMaterial;
use App\Models\material;
use App\Models\material_assigned;
use Illuminate\Support\Facades\DB;

class ReportMaterialController extends Controller
{

    // Crear un report material
    public function create(Request $request) {
       
        try {
            $reports = $request->all();
    
            foreach ($reports as $report) {
                $materialAssignedId = $report['id_material_assigned'];
                $amountLeftOver = $report['amount_left_over'];
    
                $existingReport = reportMaterial::where('id_material_assigned', $materialAssignedId)->first();
    
                if ($existingReport) {
                    // Si ya existe un reporte, no se permite crear otro
                    return response()->json(['msg' => 'Ya existe un reporte para este material asignado, no necesita reportar el material.'], 409);
                }
    
                $newReport = reportMaterial::create($report);

            }
    
       
            return response()->json(['msg' => 'Se crearon los reportes de material con éxito.', 'data' => $reports], 201);
    
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Ocurrió un error al crear los reportes o actualizar el stock.'], 500);
        }
    }

    //Obtener el reporte del material en función de eviction_completed si se necesita el desalojo
    public function getReportMaterialByEviction (Request $request) {
        try {

            $perPage = intval($request->input('per_page'));
            $currentPage = intval($request->input('page'));

            $results = DB::table('report_materials as rm')
            ->join('material_assigneds as ma', 'rm.id_material_assigned', '=', 'ma.id_material_assigned')
            ->join('materials as m', 'ma.id_material', '=', 'm.id_material')
            ->join('work_orders as wo', 'ma.id_work_order', '=', 'wo.id_work_order')
            ->join('jobs', 'wo.id_job', '=', 'jobs.id_job')
            ->join('establishments as est', 'jobs.id_establishment', '=', 'est.id_establishment')
            ->select(
                'rm.id_report_material', 
                'rm.amount_left_over', 
                'rm.eviction_completed',
                'ma.id_material_assigned',
                'ma.amount',
                'ma.delivered_amount',
                'm.id_material',
                'm.name_material',
                'm.description as material_description',
                'm.image as image_material',
                'm.unit_value',
                'm.stock',
                'm.total_value',
                'wo.instructions',
                'wo.assigned_date',
                'wo.end_date',
                'wo.hour_job',
                'jobs.name_job',
                'jobs.description as job_description',
                'jobs.num_caf',
                'est.name_establishment',
                'est.description as establishment_description',
                'est.location'
            )
            ->where('rm.eviction_completed', false)
            ->get();
            
            return response()->json($results);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo obtener los materiales en los que hay qye hacer el desalojo']);
        }
    }

    //Ajusta el stock del material y finaliza el desalojo
    public function completedReportMaterial ($idReportMaterial) {
        try {

            \DB::beginTransaction();
            $reportMaterial = reportMaterial::findOrFail($idReportMaterial);
            $reportMaterial->eviction_completed = true;
            $reportMaterial->save();

            $materialAssignedId = $reportMaterial->id_material_assigned;
            $amountLeftOver = $reportMaterial->amount_left_over;

            // Incrementa el stock del material y carga las relaciones necesarias
            $materialAssigned = material_assigned::findOrFail($materialAssignedId);
            $material = material::with(['magnitude_value', 'magnitude_value.magnitude'])
                ->findOrFail($materialAssigned->id_material);
            $material->increment('stock', $amountLeftOver); // Incrementa el stock del material
            $material->total_value = $material->stock * $material->unit_value; // Recalcula el valor total
            $material->save();

            $response = [
                "id_material" => $material->id_material,
                "name_material" => $material->name_material,
                'description' => $material->description,
                'image' => $material->image,
                'unit_value' => $material->unit_value,
                'stock' => $material->stock,
                'total_value' => $material->total_value,
                'value' => $material->magnitude_value->value,
                'name' => $material->magnitude_value->magnitude->name,
                'symbol' => $material->magnitude_value->magnitude->symbol

            ];

            \DB::commit();
            return response()->json(['data' =>  $response , 'msg' => 'Se ajusto el stock del inventario']);

        } catch (\Throwable $th) {
            \DB::rollBack();
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo finalizar el desalojo, intentelo de nuevo']);
        }
    }

}