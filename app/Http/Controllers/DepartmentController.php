<?php

namespace App\Http\Controllers;

use App\Models\department;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDepartment;

class DepartmentController extends Controller
{   

    //Obtiene todo los datos
    public function get(){
        try {
            $data = department::get(); //Devuelve una colección
            return response()->json($data, 200);

        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }

    //Obtiene el nombre del departamento con el id
    public static function getNameDepartmentById ($id) {
        try {
            $department = Department::select('name_department')->find($id);
            if(!$department) {
                $department = ['name_department' => 'Sin departamento'];
            }

            return $department;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //Obtiene los datos de departamento y cantidad de usuarios
    public function getDepartmentUsers(){
        try {
            $results = Department::leftJoin('users', 'departments.id_department', '=', 'users.id_department')
                ->selectRaw('
                    SUM(CASE WHEN users.id_rol NOT IN (4, 5) THEN 1 ELSE 0 END) as "users_quantity",
                    departments.id_department,
                    departments.name_department,
                    departments.description'
                )
                ->groupBy('departments.id_department', 'departments.name_department', 'departments.description')
                ->orderBy('users_quantity', 'desc')
                ->get();
    
            return response()->json($results, 200);
    
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }

    //Obtiene los datos de departamento y cantidad de usuario sin el departamento de administración
    public function getDepartment () {
        try {

            $idDepartment = 1;
            $results = Department::leftJoin('users', 'departments.id_department', '=', 'users.id_department')
            ->selectRaw('
                SUM(CASE WHEN users.id_rol NOT IN (4, 5) THEN 1 ELSE 0 END) as "users_quantity",
                departments.id_department,
                departments.name_department,
                departments.description'
            )
            ->where('departments.id_department', '!=', $idDepartment)
            ->groupBy('departments.id_department', 'departments.name_department', 'departments.description')
            ->orderBy('users_quantity', 'desc')
            ->get();

            return response()->json($results, 200);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo obtener los datos de los departamentos, intentelo de nuevo'
            , 'error' => $th->getMessage()], 500);
        }
    }
    

    //Crea un nuevo registro en la tabla
    public function create(StoreDepartment $request){ //LLamo al Requests de storeDepartment para validar
        try {
            $res = department::create($request->all()); //crea el registro en la tabla
            return response()->json (['msg' => 'Se creo un nuevo departamento', 'data' => $res], 201);

        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }


    //Obtiene un registro por el id
    public function getById($id) { 
        try {
            $data = department::find($id); //Find devuelve un modelo
            if(!$data) {
                return response()->json (['msg' => 'No se encontraron los registros'], 404);
            }
            return response()->json($data, 200);

        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500); 
        }
    }


    //Actualiza un registro
    public function update(Request $request, $id) {
        try {
            $data = department::find($id);
            if (!$data) {
                return response()->json(['msg' => 'No se encontró el departamento'], 404);
            }
    
            $res = $data->update($request->all()); // Actualiza los datos
    
            if ($res) {
                return response()->json(['msg' => 'Se actualizó correctamente', 'data' => $data], 200);
            } else {
                return response()->json(['msg' => 'No se pudo actualizar el departamento'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500); 
        }
    }
    


    //Elimina un registro

    public function delete ($id_department){
        try {
            // Verificar si hay usuarios asociados al departamento
            $usersCount = Department::leftJoin('users', 'departments.id_department', '=', 'users.id_department')
            ->where('departments.id_department', $id_department)
            ->count('users.id_user');  

            if ($usersCount > 0) { //Existen usuarios asociados
                return response()->json(['msg' => 'No se puede eliminar el departamento porque tiene usuarios asociados'], 400);
            }

            Department::where('id_department', $id_department)->delete(); //Elimina el departamento

            return response()->json(['msg' => 'Departamento eliminado exitosamente']);

        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }


    // Obtener todos los departamentos y sus trabajos relacionados
    public function getAssignDepartment() {
        try {
            
            $departments = Department::with('jobs')->paginate(10); //Paginación
            return response()->json($departments, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo obtener la información'], 500);
        }
    }

    // Obtener los  trabajos de un departamento en particular
    public function getAssignDepartmentByID($id, Request $request)
    {
        try {
            $department = Department::findOrFail($id);
            $perPage = $request->input('per_page', 10); 
            $jobs = $department->jobs()->paginate($perPage);
    
            // Devuelve el departamento junto con los trabajos paginados
            return response()->json(['department' => $department, 'jobs' => $jobs], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo obtener la información'], 500);
        }
    }

      // Obtener los  trabajos de un departamento en particular x Fecha
      public function getAssignDepartmentByDate($id, Request $request)
      {
          try {

              $department = Department::findOrFail($id);
              $jobs = $department->jobs()->get();
    
              
              // Devuelve el departamento junto con los trabajos paginados
              return response()->json(['data' => $jobs], 200);
          } catch (\Throwable $th) {
              return response()->json(['error' => $th->getMessage(), 'msg' => 'Algo salió mal, no se pudo obtener la información'], 500);
          }
      }

    //Obtener a los departamentos menos el de administración
    public function getDepartmentWithOutAdmin () {
        try {
            $idDepartmentAdmin = 1;

            $department = department::where('id_department', '!=', $idDepartmentAdmin)->get();
            return response()->json($department);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'msg' => 'No se pudo obtener los departamentos']);
        }
    }
    


}
