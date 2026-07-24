<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrabajoRepuesto;
use App\Models\TrabajoMantenimiento;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class TrabajoRepuestoController extends Controller
{
    #[OA\Get(
        path: "/api/trabajo-repuestos",
        summary: "Listar uso de repuestos",
        tags: ["Uso de Repuestos"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => TrabajoRepuesto::where('estado', 1)->with(['trabajoMantenimiento', 'repuesto'])->get()
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los trabajo-repuestos.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Post(
        path: "/api/trabajo-repuestos",
        summary: "Registrar repuesto a un trabajo",
        tags: ["Uso de Repuestos"],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "id_trabajo_mantenimiento", type: "integer"),
                    new OA\Property(property: "id_repuesto", type: "integer"),
                    new OA\Property(property: "cantidad", type: "integer"),
                    new OA\Property(property: "observacion", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Asignación exitosa")
        ]
    )]
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'id_trabajo_mantenimiento' => 'required|exists:trabajos_mantenimiento,id_trabajo_mantenimiento',
                'id_repuesto' => 'required|exists:repuestos,id_repuesto',
                'cantidad' => 'required|integer|min:1',
                'observacion' => 'nullable|string|max:255',
                'estado' => 'sometimes|boolean'
            ]);

            $data = $request->all();
            if (!isset($data['estado'])) {
                $data['estado'] = 1;
            }

            $trabajoRepuesto = TrabajoRepuesto::create($data);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Trabajo-Repuesto registrado correctamente.',
                'data' => $trabajoRepuesto->load(['trabajoMantenimiento', 'repuesto'])
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el trabajo-repuesto.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/api/trabajo-repuestos/{id}",
        summary: "Ver registro de repuesto por ID",
        tags: ["Uso de Repuestos"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function show($id)
    {
        try {

            $trabajoRepuesto = TrabajoRepuesto::with(['trabajoMantenimiento', 'repuesto'])->find($id);

            if (!$trabajoRepuesto) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo-Repuesto no encontrado.'
                ], 404);

            }

            return response()->json([
                'success' => true,
                'data' => $trabajoRepuesto
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el trabajo-repuesto.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Put(
        path: "/api/trabajo-repuestos/{id}",
        summary: "Actualizar registro de repuesto",
        tags: ["Uso de Repuestos"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "cantidad", type: "integer"),
                    new OA\Property(property: "observacion", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Actualizado exitosamente")
        ]
    )]
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $trabajoRepuesto = TrabajoRepuesto::where('estado', 1)->find($id);

            if (!$trabajoRepuesto) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo-Repuesto no encontrado.'
                ], 404);

            }

            $request->validate([
                'id_trabajo_mantenimiento' => 'sometimes|exists:trabajos_mantenimiento,id_trabajo_mantenimiento',
                'id_repuesto' => 'sometimes|exists:repuestos,id_repuesto',
                'cantidad' => 'sometimes|integer|min:1',
                'observacion' => 'nullable|string|max:255',
                'estado' => 'sometimes|boolean'
            ]);

            $trabajoRepuesto->update($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Trabajo-Repuesto actualizado correctamente.',
                'data' => $trabajoRepuesto->load(['trabajoMantenimiento', 'repuesto'])
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el trabajo-repuesto.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Delete(
        path: "/api/trabajo-repuestos/{id}",
        summary: "Remover registro de repuesto (Soft Delete)",
        tags: ["Uso de Repuestos"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Eliminado correctamente")
        ]
    )]
    public function destroy($id)
{
    try {
        DB::beginTransaction();
        
        $trabajo = TrabajoMantenimiento::where('estado', 1)->find($id);

        if (!$trabajo) {
            return response()->json([
                'success' => false,
                'message' => 'Trabajo de mantenimiento no encontrado.'
            ], 404);
        }

        $trabajo->update(['estado' => 0]);
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Trabajo de mantenimiento eliminado correctamente (estado = 0).'
        ], 200);

    } catch (Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar el trabajo de mantenimiento.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function restore($id)
    {
        try {
            DB::beginTransaction();
            $trabajoRepuesto = TrabajoRepuesto::where('estado', 0)->find($id);

            if (!$trabajoRepuesto) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo-Repuesto no encontrado o no está eliminado.'
                ], 404);

            }

            $trabajoRepuesto->update(['estado' => 1]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Trabajo-Repuesto restaurado correctamente.',
                'data' => $trabajoRepuesto->load(['trabajoMantenimiento', 'repuesto'])
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar el trabajo-repuesto.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function allWithDeleted()
    {
        try {

            $trabajoRepuestos = TrabajoRepuesto::with(['trabajoMantenimiento', 'repuesto'])->get();

            return response()->json([
                'success' => true,
                'data' => $trabajoRepuestos,
                'message' => 'Todos los trabajo-repuestos obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener todos los trabajo-repuestos.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function trashed()
    {
        try {

            $trabajoRepuestos = TrabajoRepuesto::where('estado', 0)
                ->with(['trabajoMantenimiento', 'repuesto'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trabajoRepuestos,
                'message' => 'Trabajo-repuestos eliminados obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los trabajo-repuestos eliminados.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function forceDelete($id)
    {
        try {

            $trabajoRepuesto = TrabajoRepuesto::find($id);

            if (!$trabajoRepuesto) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo-Repuesto no encontrado.'
                ], 404);

            }

            $trabajoRepuesto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Trabajo-Repuesto eliminado físicamente de la base de datos.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar físicamente el trabajo-repuesto.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function byTrabajo($idTrabajo)
    {
        try {

            $trabajo = TrabajoMantenimiento::find($idTrabajo);

            if (!$trabajo) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo de mantenimiento no encontrado.'
                ], 404);

            }

            $trabajoRepuestos = TrabajoRepuesto::where('id_trabajo_mantenimiento', $idTrabajo)
                ->with(['trabajoMantenimiento', 'repuesto'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trabajoRepuestos,
                'message' => 'Repuestos del trabajo obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los repuestos por trabajo.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function byRepuesto($idRepuesto)
    {
        try {

            $repuesto = Repuesto::where('estado', 1)->find($idRepuesto);

            if (!$repuesto) {

                return response()->json([
                    'success' => false,
                    'message' => 'Repuesto no encontrado.'
                ], 404);

            }

            $trabajoRepuestos = TrabajoRepuesto::where('id_repuesto', $idRepuesto)
                ->with(['trabajoMantenimiento', 'repuesto'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trabajoRepuestos,
                'message' => 'Trabajos del repuesto obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los trabajos por repuesto.',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}
