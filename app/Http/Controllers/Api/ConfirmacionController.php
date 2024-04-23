<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ConfirmacionRequest;
use App\Http\Resources\ActividadResource;
use App\Http\Resources\ConfirmacionResource;
use App\Models\Actividad;
use App\Models\Confirmacion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OneSignal;


/**
 * ConfirmacionController handles the CRUD operations for Confirmacion model.
 */
class ConfirmacionController extends ApiController
{
    /**
     * Display a listing of Confirmaciones.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // query confirmacions and aply filters and sort with db::table
            $confirmaciones = DB::table('confirmaciones')
                ->select('id', 'user_id', 'actividad_id');
            $allowedFilterFields = ['user_id', 'actividad_id'];
            $allowedSortFields = ['user_id', 'actividad_id'];
            $this->applyFilters($confirmaciones, $request, $allowedFilterFields);
            $this->applySorts($confirmaciones, $request, $allowedSortFields);

            $confirmaciones = $confirmaciones->get();

            $result = [
                'confirmaciones' => ConfirmacionResource::collection($confirmaciones)
            ];
            $message = 'Confirmaciones recuperadas correctamente';

            return $this->sendResponse($result, $message);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage(), []);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created Confirmacion.
     *
     * @param ConfirmacionRequest $request
     * @return JsonResponse
     */
    public function store(ConfirmacionRequest $request): JsonResponse
    {
        // validate confirmacionRequest
        $validated = $request->validated();

        try {
            $confirmacion = Confirmacion::where([
                'user_id' => $validated['user_id'],
                'actividad_id' => $validated['actividad_id'],
            ])->first();
            if ($confirmacion) {
                $message = 'Confirmacion ya existe';
                // return $this->sendError($message, $message, 400);
            }

            DB::beginTransaction();

            $confirmacion = Confirmacion::create($validated);

            $result = [
                'confirmacion' => new ConfirmacionResource($confirmacion),
            ];
            $message = 'Confirmacion creado correctamente';
            //end transaction
            DB::commit();

            $asistentes = DB::table('confirmaciones')
                ->where('actividad_id', '=', $confirmacion->actividad_id)
                ->join('userdatas', 'userdatas.user_id', '=', 'confirmaciones.user_id')
                ->select('userdatas.id', 'userdatas.nombre', 'userdatas.edad', 'userdatas.genero', 'userdatas.foto', 'userdatas.onesignal_id')
                ->get();                //

            foreach ($asistentes as $asistente) {
                $user_id = $asistente->onesignal_id;
                if (!$asistente->onesignal_id == null) {
                    $nombreActividad = Actividad::findOrFail($confirmacion->actividad_id)->nombre;
//                    $notificacion = OneSignal::sendNotificationToUser(
//                        'Se ha confirmado nuevo asistente a la actividad: ' . $nombreActividad,
//                        $asistente->onesignal_id,
//                        $url = null,
//                        $data = null,
//                        $buttons = null,
//                        $schedule = null
//                    );
                    $fields['include_player_ids'] = [$asistente->onesignal_id];
                    $notificationMsg = 'Hello!! A tiny web push notification.!';
                    OneSignal::sendPush($fields, $notificationMsg);
                    $result['notificacion'] = [$asistente->onesignal_id => $notificationMsg];

                    Log::info('Notificacion enviada a usuario', ['asistente' => $asistente->nombre, 'confirmacion_id' => $confirmacion->id]);

                }
            }
            Log::info($message, ['confirmacion_id' => $confirmacion->id]);

            return $this->sendResponse($result, $message);

        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollBack();
            Log::error('Confirmacion creation failed: ' . $e->getMessage(), ['confirmacion' => $request->all()]);
            return $this->sendError('Confirmacion creation failed: ', $e->getMessage(), 500);
        }


    }

    /**
     * Display the specified Confirmacion.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $confirmacion = Confirmacion::findOrFail($id);
            $confirmacionResource = new ConfirmacionResource($confirmacion);

            $actividad = Actividad::findOrFail($confirmacion->actividad_id);
            $actividadResource = new ActividadResource($actividad);

            $asistentes = DB::table('confirmaciones')
                ->where('actividad_id', '=', $actividad->id)
                ->join('userdatas', 'userdatas.user_id', '=', 'confirmaciones.user_id')
                ->select('userdatas.id', 'userdatas.nombre', 'userdatas.edad', 'userdatas.genero', 'userdatas.foto', 'userdatas.onesignal_id')
                ->get();                //

            foreach ($asistentes as $asistente) {
                $user_id = $asistente->id;
                $asistente->foto = UserData::getFoto($user_id);
                $asistente->genero = UserData::getGenero($user_id);
                if (!$asistente->onesignal_id == null) {
                    OneSignal::sendNotificationToUser(
                        "Se ha confirmado nuevo asistente a la actividad: " . $actividad->nombre,
                        $asistente->onesignal_id,
                        $url = null,
                        $data = null,
                        $buttons = null,
                        $schedule = null
                    );
                }
            }

            $result = [
                'confirmacion' => $confirmacionResource,
                'actividad' => $actividadResource,
                'asistentes' => $asistentes
            ];
            $message = 'Confirmacion recuperados correctamente';

            return $this->sendResponse($result, $message);
        } catch (ModelNotFoundException $e) {
            Log::error('Confirmacion not found: ' . $id . '. ' . $e->getMessage());
            return $this->sendError('Confirmacion not found:' . $id, $e->getMessage(), 404);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage(), ['id' => $id]);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified Confirmacion.
     *
     * @param ConfirmacionRequest $request
     * @param int|null $id
     * @return JsonResponse
     */
    public function update(ConfirmacionRequest $request, $id = null): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            DB::beginTransaction();

            // find confirmacion by id
            $confirmacion = Confirmacion::findOrFail($id);
            $confirmacion->update($validatedData);
            $confirmacionResource = new ConfirmacionResource($confirmacion);

            $result = [
                'confirmacion' => $confirmacionResource,
            ];
            $message = 'Confirmacion actualizado correctamente';
            DB::commit();
            Log::info($message, ['confirmacion_id' => $confirmacion->id]);
            return $this->sendResponse($result, $message);
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollBack();
            $message = 'Confirmacion update failed: ';//end transaction

            Log::error($message . $e->getMessage(), ['confirmacion' => $id]);
            return $this->sendError('Confirmacion update failed: ', $e->getMessage(), 500);
        }

    }

    /**
     * Remove the specified Confirmacion from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {

        try {
            DB::beginTransaction();
            $confirmacion = Confirmacion::findOrFail($id);
            $confirmacion->delete();
            $result = [
                'deletedata' => $confirmacion,
            ];
            $message = 'Confirmacion desactivada correctamente';
            DB::commit();
            Log::info($message, ['confirmacion_id' => $confirmacion->id]);
            return $this->sendResponse($result, $message);
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollBack();
            Log::error('Confirmacion deletion failed: ' . $e->getMessage(), ['confirmacion' => $id]);
            return $this->sendError('Confirmacion deletion failed: ', $e->getMessage(), 500);
        }

    }

    /**
     * Show the form for editing the specified Confirmacion.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function edit($id): JsonResponse
    {
        try {
            $confirmacion = Confirmacion::findOrFail($id);
            $result = [
                'confirmacion' => $confirmacion,
            ];
            return $this->sendResponse($result, 'Confirmacion edit retrieved successfully');
        } catch (ModelNotFoundException $e) {
            Log::error('Confirmacion not found: ' . $id . '. ' . $e->getMessage());
            return $this->sendError('Confirmacion not found: ' . $id, $e->getMessage(), 404);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage(), ['id' => $id]);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }
    }
}
