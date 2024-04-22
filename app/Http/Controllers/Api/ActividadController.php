<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ActividadRequest;
use App\Http\Resources\ActividadResource;
use App\Models\Actividad;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActividadController extends ApiController
{
    public function index(Request $request)
    {
        try {
            // get all actividades only nombre, foto descripcion and fecha fields, Apply filters and sort using Actividad class
            //$actividades = Actividad::all('nombre', 'foto', 'descripcion', 'fecha');

            // query actividads and aply filters and sort with db::table
            $actividades = DB::table('actividades')
                ->select('id','nombre', 'foto', 'descripcion', 'fecha');

            $allowedFilterFields = ['nombre', 'edad', 'genero']; // Define allowed fields for filtering
            $allowedSortFields = ['nombre', 'fecha']; // Define allowed fields for sorting
            $this->applyFilters($actividades, $request, $allowedFilterFields);
            $this->applySorts($actividades, $request, $allowedSortFields);

            $actividades = $actividades->get();

            $result = [
                'actividades' => ActividadResource::collection($actividades)
            ];
            $message = 'Actividades recuperadas correctamente';

            return $this->sendResponse($result, $message);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage(), []);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }
    }

    /**
     * @param ActividadRequest $request
     * @return JsonResponse
     */
    public function store(ActividadRequest $request): JsonResponse
    {
        // validate actividadRequest
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $actividad = Actividad::create($validated);

            $actividad = $validated + ['actividad_id' => $actividad->id, 'nombre' => $actividad->name];
            $actividad = Actividad::create($actividad);

            $result = [
                'actividad' => new ActividadResource($actividad),
            ];
            $message = 'Actividad creado correctamente';
            //end transaction
            DB::commit();
            Log::info($message, ['actividad_id' => $actividad->id]);

//            if (!$request->wantsJson()) { // check InteractsWithContentTypes trait to see all the methods available
//                return view('actividad.create', $result)->with('success', 'Actividad created successfully');
//
//            }
// return redirect response
            //return redirect()->route('some.route')->with('success', 'Actividad created successfully');
            return $this->sendResponse($result, $message);

            //return redirect()->route('form_view', ['id' => $id]);
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollBack();
            Log::error('Actividad creation failed: ' . $e->getMessage(), ['actividadname' => $request->email]);
            return $this->sendError('Actividad creation failed: ', $e->getMessage(), 500);
        }


    }

    public function show(int $id)
    {
        try {
            $actividad = Actividad::findOrFail($id);
            $actividadResource = new ActividadResource($actividad);

            $result = [
                'actividad' => $actividadResource,
            ];
            $message = 'Userdatas recuperados correctamente';

            return $this->sendResponse($result, $message);
        } catch (ModelNotFoundException $e) {
            Log::error('Actividad not found: ' . $id . '. ' . $e->getMessage());
            return $this->sendError('Actividad not found:' . $id, $e->getMessage(), 404);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage(), ['id' => $id]);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }
    }

    public function update(ActividadRequest $request, $id = null): JsonResponse
    {

        try {
            $validatedData = $request->validated();

            DB::BeginTransaction();

            // find actividad by id
            $actividad = Actividad::findOrFail($id);
            // find actividad by actividad_id

            $actividad->update([
                'nombre' => $validatedData['nombre'],
                'foto' => $validatedData['foto'],
                'fecha' => $validatedData['fecha'],
                'descripcion' => $validatedData['descripcion']
            ]);

            $actividadResource = new ActividadResource($actividad);

            $result = [
                'actividad' => $actividadResource,
            ];
            $message = 'Actividad actualizado correctamente';
            //end transaction
            DB::commit();
            Log::info($message, ['actividad_id' => $actividad->id]);
            return $this->sendResponse($result, $message);
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollBack();
            $message = 'Actividad update failed: ';//end transaction

            Log::error($message . $e->getMessage(), ['actividad' => $id]);
            return $this->sendError('Actividad update failed: ', $e->getMessage(), 500);
        }

    }

    public function destroy(int $id)
    {
        try {
            DB::beginTransaction();

            // find actividad by id
            $actividad = Actividad::findOrFail($id);
            // find actividad by actividad_id

            $actividad->update([
                'acive' => 'false'
            ]);

            $actividadResource = new ActividadResource($actividad);

            $result = [
                'actividad' => $actividadResource,
            ];
            $message = 'Actividad desactivada correctamente';
            //end transaction
            DB::commit();
            Log::info($message, ['actividad_id' => $actividad->id]);
            return $this->sendResponse($result, $message);
        } catch (\Exception $e) {
            // Rollback transaction on failure
            DB::rollBack();
            $message = 'Actividad update failed: ';//end transaction

            Log::error($message . $e->getMessage(), ['actividad' => $id]);
            return $this->sendError('Actividad update failed: ', $e->getMessage(), 500);
        }

    }

    public function edit($id): JsonResponse
    {
        try {
            $actividad = Actividad::findOrFail($id);

            $result = [
                'actividad' => $actividad,
            ];
            //redirect to form view, who renders form to edit actividad

            //return view('form_view', $result);

            return $this->sendResponse($result, 'Actividad edit retrieved successfully');
        } catch (ModelNotFoundException $e) {
            Log::error('Actividad not found: ' . $id . '. ' . $e->getMessage());
            return $this->sendError('Actividad not found: ' . $id, $e->getMessage(), 404);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage(), ['id' => $id]);
            return $this->sendError('An error occurred', $e->getMessage(), 500);
        }
    }
}
