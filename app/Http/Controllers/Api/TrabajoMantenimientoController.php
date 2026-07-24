<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrabajoMantenimiento;
use App\Models\Mantenimiento;
use App\Models\Diagnostico;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class TrabajoMantenimientoController extends Controller
{
    #[OA\Get(
        path: "/trabajos-mantenimiento",
        summary: "Listar todos los trabajos",
        tags: ["Trabajos de Mantenimiento"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => TrabajoMantenimiento::with(['mantenimiento', 'diagnostico'])->get()
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los trabajos de mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Post(
        path: "/trabajos-mantenimiento",
        summary: "Registrar trabajo de mantenimiento",
        tags: ["Trabajos de Mantenimiento"],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "id_diagnostico", type: "integer"),
                    new OA\Property(property: "id_mantenimiento", type: "integer"),
                    new OA\Property(property: "fecha_programada", type: "string", format: "date"),
                    new OA\Property(property: "fecha_inicio", type: "string", format: "date-time"),
                    new OA\Property(property: "estado", type: "string", enum: ["pendiente", "en_proceso", "finalizado", "cancelado"])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Creado exitosamente")
        ]
    )]
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'id_diagnostico' => 'required|exists:diagnosticos,id_diagnostico',
                'id_mantenimiento' => 'required|exists:mantenimientos,id_mantenimiento',
                'fecha_programada' => 'nullable|date',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'observaciones' => 'nullable|string',
                'estado' => 'required|in:pendiente,en_proceso,finalizado,cancelado'
            ]);

            $trabajo = TrabajoMantenimiento::create($request->all());
            DB::commit();   
            return response()->json([
                'success' => true,
                'message' => 'Trabajo de mantenimiento registrado correctamente.',
                'data' => $trabajo->load(['mantenimiento', 'diagnostico'])
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el trabajo de mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/trabajos-mantenimiento/{id}",
        summary: "Obtener trabajo por ID",
        tags: ["Trabajos de Mantenimiento"],
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

            $trabajo = TrabajoMantenimiento::with(['mantenimiento', 'diagnostico', 'asignaciones', 'trabajoHerramientas', 'trabajoRepuestos'])->find($id);

            if (!$trabajo) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo de mantenimiento no encontrado.'
                ], 404);

            }

            return response()->json([
                'success' => true,
                'data' => $trabajo
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el trabajo de mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Put(
        path: "/trabajos-mantenimiento/{id}",
        summary: "Actualizar estado del trabajo",
        tags: ["Trabajos de Mantenimiento"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "estado", type: "string", enum: ["pendiente", "en_proceso", "finalizado", "cancelado"]),
                    new OA\Property(property: "observaciones", type: "string")
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
            $trabajo = TrabajoMantenimiento::find($id);

            if (!$trabajo) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo de mantenimiento no encontrado.'
                ], 404);

            }

            $request->validate([
                'id_diagnostico' => 'sometimes|exists:diagnosticos,id_diagnostico',
                'id_mantenimiento' => 'sometimes|exists:mantenimientos,id_mantenimiento',
                'fecha_programada' => 'nullable|date',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
                'observaciones' => 'nullable|string',
                'estado' => 'sometimes|in:pendiente,en_proceso,finalizado,cancelado'
            ]);

            $trabajo->update($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Trabajo de mantenimiento actualizado correctamente.',
                'data' => $trabajo->load(['mantenimiento', 'diagnostico'])
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el trabajo de mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Delete(
        path: "/trabajos-mantenimiento/{id}",
        summary: "Eliminar trabajo Físicamente",
        tags: ["Trabajos de Mantenimiento"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Eliminado físicamente")
        ]
    )]
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $trabajo = TrabajoMantenimiento::find($id);

            if (!$trabajo) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo de mantenimiento no encontrado.'
                ], 404);

            }

            $trabajo->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Trabajo de mantenimiento eliminado correctamente.'
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

    #[OA\Get(
        path: "/trabajos-mantenimiento/estado/{estado}",
        summary: "Obtener trabajos por estado",
        tags: ["Trabajos de Mantenimiento"],
        parameters: [
            new OA\Parameter(
                name: "estado",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", enum: ["pendiente", "en_proceso", "finalizado", "cancelado"])
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function byEstado($estado)
    {
        try {
            
            $estadosValidos = ['pendiente', 'en_proceso', 'finalizado', 'cancelado'];

            if (!in_array($estado, $estadosValidos)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Estado no válido. Debe ser: pendiente, en_proceso, finalizado o cancelado.'
                ], 400);

            }

            $trabajos = TrabajoMantenimiento::where('estado', $estado)
                ->with(['mantenimiento', 'diagnostico'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trabajos,
                'message' => 'Trabajos con estado ' . $estado . ' obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los trabajos por estado.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/trabajos-mantenimiento/mantenimiento/{idMantenimiento}",
        summary: "Obtener trabajos por tipo de mantenimiento",
        tags: ["Trabajos de Mantenimiento"],
        parameters: [
            new OA\Parameter(name: "idMantenimiento", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function byMantenimiento($idMantenimiento)
    {
        try {

            $mantenimiento = Mantenimiento::where('estado', 1)->find($idMantenimiento);

            if (!$mantenimiento) {

                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado.'
                ], 404);

            }

            $trabajos = TrabajoMantenimiento::where('id_mantenimiento', $idMantenimiento)
                ->with(['mantenimiento', 'diagnostico'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trabajos,
                'message' => 'Trabajos del mantenimiento obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los trabajos por mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}
