<?php
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

//Canal publico de Prueba
Broadcast::channel('home', function () {
  return true;
});

//Canal privado de prueba,
Broadcast::channel('private-test.{id}', function (user $user, $id) {
    return (int) $user->id_user === (int) $id;
});


//Canal de usuarios privados
Broadcast::channel('userAssignedJob.{id}', function (user $user, $id) {
  return (int) $user->id_user === (int) $id;
});


//Canal privado de usuario para reporte de material
Broadcast::channel('materialReport.{id}', function (user $user, $id) {
  return (int) $user->id_user === (int) $id;
});

//Canal privado de usuario para reporte de equipo
Broadcast::channel('equipmentReport.{id}', function (user $user, $id) {
  return (int) $user->id_user === (int) $id;
});

//Canal privado de usuario para reporte de material no disponible que se necesita para un trabajo
Broadcast::channel('materialUnavailable.{id}', function (user $user, $id) {
  return (int) $user->id_user === (int) $id;
});

//Canal privado de usuario para repoartar un equipo que necesite para un trabajo y no este en el stock
Broadcast::channel('equipmentUnavailable.{id}', function (user $user, $id) {
  return (int) $user->id_user === (int) $id;
});

//Canal de las notificaciones (No funciono el broadcast)
Broadcast::channel('App.Models.User.{id}', function (user $user, $id) {
  return (int) $user->id_user === (int) $id;
});




