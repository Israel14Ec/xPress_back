<?php

namespace App\Http\Controllers;

use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUser;
use App\Http\Requests\StoreUserEdit;
use Illuminate\Support\Str;
use App\Notifications\mailResetPass;
use Carbon\Carbon;

class UserController extends Controller
{
    // Método para crear un nuevo usuario
    public function create(StoreUser $request)
    {
        try {
            $data = [
                'name' => $request->input('name'),
                'last_name' => $request->input('last_name'),
                'phone_number' => $request->input('phone_number'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'id_department' => $request->input('id_department'),
            ];

            user::create($data); // Crea el registro en la tabla

            return response()->json(['msg' => 'Cuenta creada correctamente'], 201);
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo crear la cuenta inténtelo mas tarde' ,
            'error' => $th->getMessage()], 500);
        }
    }

    //Metodo para crear a un administrador
    public function createAdmin (StoreUser $request) {
        try {
            $data = [
                'name' => $request->input('name'),
                'last_name' => $request->input('last_name'),
                'phone_number' => $request->input('phone_number'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'id_department' => $request->input('id_department'),
                'id_rol' => $request->input('id_rol')
            ];

            user::create($data);
            return response()->json(['msg' => 'Se creo una nueva cuenta de administrador'], 201);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo crear la cuenta, intentelo de nuevo', 
                'error' => $th->getMessage()], 500);
        }
    }

    //Obtiene todos los usuarios que no se les asigno un rol
    public function getUserNoRole()
    {
        try {
            $data = user::where('id_rol', 4)
                ->select('users.id_user', 'users.name', 'users.last_name', 'users.id_rol', 'users.phone_number', 'users.email', 'departments.name_department')
                ->join('departments', 'users.id_department', '=', 'departments.id_department')
                ->get();

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }

    //Obtiene a los usuarios en funcion de su rol y departamento y no obtiene su propio usuario
    public function getUserRol($id_user, $id_department, $id_rol) {
        try {
            $query = user::where('users.id_user', '!=', $id_user)
                ->where('users.id_rol', '!=', 4)
                ->where('users.id_rol', '!=', 5);
                
    
            if ($id_department) {
                $query->where('users.id_department', '=', $id_department);
            }
    
            if ($id_rol) {
                $query->where('users.id_rol', '=', $id_rol);
            }
    
            $data = $query->select('users.id_user', 'users.name', 'users.last_name', 'users.id_rol', 
                'users.phone_number', 'users.email', 
                'departments.name_department', 'departments.id_department',
                'rols.name_rol'
            )
            ->join('departments', 'users.id_department', '=', 'departments.id_department')
            ->join('rols', 'users.id_rol', '=', 'rols.id_rol')
            ->get();
    
            return response()->json($data, 200);
    
        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        }
    }

    //Obtiene a los jefes de departamento segun el departamento, es un metodo 
    public static function getDepartmentHeads($id_department)
    {
        return user::where('id_rol', 2)
               ->where('id_department', $id_department)
               ->get();
    }

    //Obtiene a los administradores
    public static function searchAdmin() {
        return user::where('id_rol', 1)
            ->get();
    }

    //Obtiene a los usuariose sin rol - no trae los datos del usuario propio si se pasa el id
    public function getUsersDepartmentNoRol(Request $request) {
        try {
            $departmentId = $request->input('id_department');
    
            $query = user::where('id_department', '=', $departmentId)
                        ->where('id_rol', 4);
       
            // Ejecuta la consulta y obtiene los resultados
            $data = $query->get();
    
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg'=> 'No se pudo obtener los datos', 'error' => $th->getMessage()], 500);
        }
    }

    
    //Obtiene los usuarios invalidos
    public function getInvalidUser(){
        try {
            $data = user::where('users.id_rol', '=', '5')->select('users.id_user', 'users.name', 'users.last_name', 'users.id_rol', 
            'users.phone_number', 'users.email', 
            'departments.name_department', 'departments.id_department',
            'rols.name_rol'
            )
            ->join('departments', 'users.id_department', '=', 'departments.id_department')
            ->join('rols', 'users.id_rol', '=', 'rols.id_rol')
            ->get();

            return response()->json($data, 200);

        } catch (\Throwable $th) {
            return response()->json(['msg' => $th->getMessage()], 500);
        } 
    }
    
    //Obtiene los usuarios invalidos segun el departamento
    public function getInvalidUserDepartment(Request $request) {
        try {
            $departmentId = $request->input('id_department');
            $data = user::where('users.id_rol', '5')
                        ->where('id_department', $departmentId)
                        ->get();
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'Algo salio mal no se pudo obtener los datos', 'error' => $th->getMessage()], 500);
        }
    }

    //Obtiene a los usuarios empleados del departamente que esten disponibles en esa fecha
    public function getUserAvailable (Request $request) {
        try {
            
            $departmentId = $request->input('id_department');
            $assignedDate = $request->input('assigned_date');
            $endDate = $request->input('end_date');
            
            $assignedDateFormatted = Carbon::createFromFormat('d/m/Y', $assignedDate)->format('Y-m-d');
            $endDateFormatted = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');

            $availableUsers = user::where('id_department', $departmentId)
            ->whereIn('id_rol', [3])
            ->whereDoesntHave('assignedWorkOrders', function ($query) use ($assignedDateFormatted, $endDateFormatted) {
                $query->where(function ($query) use ($assignedDateFormatted, $endDateFormatted) {
                    // El trabajo inicia o termina dentro del rango de fechas
                    $query->whereBetween('assigned_date', [$assignedDateFormatted, $endDateFormatted])
                          ->orWhereBetween('end_date', [$assignedDateFormatted, $endDateFormatted]);
                });
            })
            ->get()
            ->makeHidden(['password', 'created_at', 'updated_at']);

        return response()->json($availableUsers, 200);

        } catch (\Throwable $th) {
            return response()->json(['msg' => 'Algo salio mal no se pudo obtener los datos', 'error' => $th->getMessage()], 500);
        }
    }
    
    //Obtiene a los usuarios empleados del departamento que están disponibles en las fechas y a los que ya se les asigno el mismo trabajo - EIDT WORK-ORDER
    public function getUserAvailableEdit(Request $request) {
        try {
            $departmentId = $request->input('id_department');
            $assignedDate = $request->input('assigned_date');
            $endDate = $request->input('end_date');
            $workOrderId = $request->input('id_work_order');
            
            // Convertir las fechas del formato d/m/Y a Y-m-d
            $assignedDateFormatted = Carbon::createFromFormat('d/m/Y', $assignedDate)->format('Y-m-d');
            $endDateFormatted = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
    
            $availableUsers = User::where('id_department', $departmentId)
                ->whereIn('id_rol', [3])
                ->whereDoesntHave('assignedWorkOrders', function ($query) use ($assignedDateFormatted, $endDateFormatted, $workOrderId) {
                    $query->where(function ($subQuery) use ($assignedDateFormatted, $endDateFormatted, $workOrderId) {
                        $subQuery->where('work_orders.id_work_order', '!=', $workOrderId)
                                 ->where(function ($dateQuery) use ($assignedDateFormatted, $endDateFormatted) {
                                     $dateQuery->where('assigned_date', '<=', $assignedDateFormatted)
                                               ->where('end_date', '>=', $endDateFormatted)
                                               ->orWhereBetween('assigned_date', [$assignedDateFormatted, $endDateFormatted])
                                               ->orWhereBetween('end_date', [$assignedDateFormatted, $endDateFormatted]);
                                 });
                    });
                })
                ->get()
                ->makeHidden(['password', 'created_at', 'updated_at']);
            
            return response()->json($availableUsers, 200);
    
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => 'No se pudo obtener los datos',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    

    //Obtiene a los usuario del departamento que no estan disponibles en la fecha:
    public function getUserNoAvailable(Request $request) {
    try {
        $departmentId = $request->input('id_department');
        $assignedDate = $request->input('assigned_date');
        $endDate = $request->input('end_date');

        $noAvailableUsers = user::where('id_department', $departmentId)
            ->whereIn('id_rol', [3])
            ->whereHas('assignedWorkOrders', function ($query) use ($assignedDate, $endDate) {
                $query->where(function ($query) use ($assignedDate, $endDate) {
                    // El trabajo inicia o termina dentro del rango de fechas
                    $query->whereBetween('assigned_date', [$assignedDate, $endDate])
                          ->orWhereBetween('end_date', [$assignedDate, $endDate]);
                });
            })
            ->get()
            ->makeHidden(['password', 'created_at', 'updated_at']);

        return response()->json($noAvailableUsers, 200);
    } catch (\Throwable $th) {
        return response()->json(['msg' => 'Algo salió mal, no se pudieron obtener los datos', 'error' => $th->getMessage()], 500);
    }
}

    //Editar rol de usuario
    public function updateUserRole(Request $request, $id) {
        
        try {
            $data = user::find($id);
            if(!$data){
                return response()->json (['msg' => 'No se encontro al usuario'], 404);
            }
            $data->id_rol = $request->input('id_rol');
            $data->save();
            return response()->json(['msg' => "Se asigno el rol"], 200);
        } catch (\Throwable $th) {
            return response() ->json(['msg' => 'No se pudo modificar el rol del usuario' ,'error' => $th->getMessage()], 500);
        }
    }

    //Actualizar perfil de usuario
    public function updateUserProfile (StoreUserEdit $request, $id ) {
        try {

            $user = user::find($id);    

            if(!$user){
                return response()->json (['msg' => 'No se encontro su perfil, intentelo de nuevo'], 404);
            }
            
            // Obtener datos del cuerpo de la solicitud
            $name = $request->input('name');
            $lastName = $request->input('last_name');
            $phoneNumber = $request->input('phone_number');
            $image = $request->input('image');

            //validar que el numero de telefono no este registrado
            $phoneNumberExist = user::where('phone_number', $phoneNumber)->where('id_user', '!=',$id)->exists();
            if($phoneNumberExist) {
                return response()->json(['msg' => "El numero de telefono ya se registro por otro usuario"], 409);
            }

            // Actualizar los datos del usuario
            $user->name = $name;
            $user->last_name = $lastName;
            $user->phone_number = $phoneNumber;
            $user->image = $image;

            $user->save();

            return response()->json(['data' => $user, 'msg' => 'Se actualizaron los datos']);
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'No se pudo actualizar sus datos' ,'error' => $th->getMessage()], 500);
        }
    }

    //Eliminar imagen del usuario
    public function deleteProfileImage ($id) {
        try {
            $user = user::find($id);
            if(!$user){
                return response()->json (['msg' => 'No se encontro al usuario'], 404);
            }

            $user->image = null;
            $user->save();
            return response()->json(['msg' => "Se elimino la foto de perfil"], 200);

        } catch (\Throwable $th) {
            return response() ->json(['error' => $th->getMessage(), 'msg' => 'No se pudo eliminar, 
            intentelo de nuevo'], 500);
        }
    }


    //Eliminado logico de usuario
    public function deleteLogicUser ($id){
        try {
            $data = user::find($id);
            $data->update(['id_rol' => 5]);
            return response()->json(['msg' => 'Se quito el acceso a la aplicación'], 200);

        } catch (\Throwable $th) {
            return response() ->json(['msg' => $th->getMessage()], 500);
        }
    }

    //Elimar usuario
    public function deleteUser($id) {
        try {
            $data = user::find($id);
    
            if ($data) {
                $res = $data->delete();
                return response()->json(['msg' => 'Se eliminó correctamente', 'data' => $data], 200);
            } else {
                return response()->json(['msg' => 'No se encontró al usuario'], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['msg' => 'Error al eliminar el usuario', 'error' => $th->getMessage()], 500);
        }
    }
    
    // Autenticación Usando Sanctum --------------------------------------------------------------
    public function login(Request $request)
    {   
      
        //Validamos las credenciales
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];
        $validator = \Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json(['msg' => $validator->errors()->all()], 400);
        }

        //Autenticamos al usuario
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user(); //Obtiene al usuario para generar el token
            $token = $user->createToken('token')->plainTextToken; //Creamos el token
            return response()->json(['token' => $token, 'data' => $user], 200); //Devolvemos el token

        } else {
            return response()->json(['msg' => 'Usuario o contraseña incorrectas, intentelo de nuevo'], 401); 
        }
        
    }
    
    //Cierre de sesión
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete(); //Elimina todos los tokens del usuario creadas 
            return response()->json(['msg' => 'Sesión cerrada exitosamente'], 200);
        
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(),
            'msg' => 'Algo salió mal vuelva a intentarlo'], 501);
        }
        
    }

    //Obtiene el perfil del usuario que se logueo con el bearer token
    public function userProfile(Request $request) {
        try {
            return response()->json([
                "msg" => auth()->user() //Obtiene al usuario autenticado, con auth()-user() es una funcion de Auth
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(),
            'msg' => 'Algo salió mal vuelva a intentarlo'], 501);
        }
    }

    //Genera el token para mandar al email
    public function forgotPassword (Request $request){
        try {
            $email = $request->input('email');

            //comprobar si existe el usuario
            $user = user::where('email', $email)->first();
            if(!$user) {
                return response()->json(['msg' =>'El email no está registrado en la aplicación'], 404);
            }

            // Generar un token único
            $token = Str::random(20);

            //Guarda el token 
            $user->token = $token;
            $user->save();

            //Envío por el email
            $user->notify(new mailResetPass($token, $user->name));

            return response()->json(['msg' => 'Se envío un email a su correo electrónico']);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 
            'msg' => 'No se pudo enviar el email de recuperación, intentelo de nuevo'], 501);
        }
    }


    //Validar el token
    public function verifyPasswordResetToken (Request $request) {
        try {
            $token = $request->input('token');
            $user = user::where('token', $token)->first(); //Busca al usuario por el token
            if(!$user) {
                return response()->json(['msg'=> 'El enlace de recuperación de contraseña es incorrecto. Por favor, solicita otro enlace para restablecer tu contraseña.'], 404);
            }
            return response()->json($user);
        
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 
            'msg' => 'No se pudo enviar el email de recuperación, intentelo de nuevo'], 501);
        }
    }

    //Cambiar la contraseña
    public function updatePassword(Request $request) {
        try {
            $token = $request->input('token');
            $newPassword = $request->input('password'); 
            $user = user::where('token', $token)->first();

            if(!$newPassword) {
                return response()->json(['msg' => 'No se proporciono una contraseña'], 404);
            }
            
            if(!$user) { //
                return response()->json(['msg'=> 'El token de restablecimiento de contraseña no es válido o ha expirado. Por favor, solicita un nuevo enlace para restablecer tu contraseña.'], 404);
            }

            $passHash = Hash::make($newPassword);
            $user->token = null; //Reestablecemos el valor del token
            $user->password = $passHash;
            $user->save();

            // Eliminar todas las sesiones activas del usuario
            $user->tokens()->delete();
            
            return response()->json(['msg' => 'Se cambio la contraseña', 'data' => $user], 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 
                'msg' => 'Se produjo un error inesperado al restablecer la contraseña. Por favor, inténtalo de nuevo más tarde.'], 501);
        }
    }

}
