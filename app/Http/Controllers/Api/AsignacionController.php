<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\TrabajoMantenimiento;
use App\Models\Personal;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class AsignacionController extends Controller
{
    #[OA\Get(
        path: "/api/asignaciones",
        summary: "Listar asignaciones de personal",
        tags: ["Asignaciones de Personal"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => Asignacion::where('estado', 1)->with(['trabajoMantenimiento', 'personal'])->get()
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Post(
        path: "/api/asignaciones",
        summary: "Asignar personal a un trabajo",
        tags: ["Asignaciones de Personal"],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "id_trabajo_mantenimiento", type: "integer"),
                    new OA\Property(property: "id_personal", type: "integer"),
                    new OA\Property(property: "rol_asignacion", type: "string"),
                    new OA\Property(property: "horas_invertidas", type: "number", format: "float")
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
                'id_personal' => 'required|exists:personal,id_personal',
                'rol_asignacion' => 'required|string|max:50',
                'horas_invertidas' => 'sometimes|numeric|min:0',
                'estado' => 'sometimes|boolean'
            ]);

            $data = $request->all();
            if (!isset($data['estado'])) {
                $data['estado'] = 1;
            }

            $asignacion = Asignacion::create($data);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Asignación registrada correctamente.',
                'data' => $asignacion->load(['trabajoMantenimiento', 'personal'])
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la asignación.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/api/asignaciones/{id}",
        summary: "Ver asignación por ID",
        tags: ["Asignaciones de Personal"],
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

            $asignacion = Asignacion::with(['trabajoMantenimiento', 'personal'])->find($id);

            if (!$asignacion) {

                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada.'
                ], 404);

            }

            return response()->json([
                'success' => true,
                'data' => $asignacion
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al buscar la asignación.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Put(
        path: "/api/asignaciones/{id}",
        summary: "Actualizar asignación",
        tags: ["Asignaciones de Personal"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "horas_invertidas", type: "number", format: "float"),
                    new OA\Property(property: "rol_asignacion", type: "string")
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
            $asignacion = Asignacion::where('estado', 1)->find($id);

            if (!$asignacion) {

                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada.'
                ], 404);

            }

            $request->validate([
                'id_trabajo_mantenimiento' => 'sometimes|exists:trabajos_mantenimiento,id_trabajo_mantenimiento',
                'id_personal' => 'sometimes|exists:personal,id_personal',
                'rol_asignacion' => 'sometimes|string|max:50',
                'horas_invertidas' => 'sometimes|numeric|min:0',
                'estado' => 'sometimes|boolean'
            ]);

            $asignacion->update($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Asignación actualizada correctamente.',
                'data' => $asignacion->load(['trabajoMantenimiento', 'personal'])
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la asignación.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Delete(
        path: "/api/asignaciones/{id}",
        summary: "Remover asignación (Soft Delete)",
        tags: ["Asignaciones de Personal"],
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

            $asignacion = Asignacion::where('estado', 1)->find($id);

            if (!$asignacion) {

                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada.'
                ], 404);

            }

            $asignacion->update(['estado' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Asignación eliminada correctamente (estado = 0).'
            ], 200);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la asignación.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Patch(
        path: "/api/asignaciones/{id}/restore",
        summary: "Restaurar asignación eliminada lógicamente",
        tags: ["Asignaciones de Personal"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Restaurado exitosamente")
        ]
    )]
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            $asignacion = Asignacion::where('estado', 0)->find($id);

            if (!$asignacion) {

                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada o no está eliminada.'
                ], 404);

            }

            $asignacion->update(['estado' => 1]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Asignación restaurada correctamente.',
                'data' => $asignacion->load(['trabajoMantenimiento', 'personal'])
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar la asignación.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/api/asignaciones/all-with-deleted",
        summary: "Obtener todas las asignaciones (incluyendo eliminadas)",
        tags: ["Asignaciones de Personal"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function allWithDeleted()
    {
        try {

            $asignaciones = Asignacion::with(['trabajoMantenimiento', 'personal'])->get();

            return response()->json([
                'success' => true,
                'data' => $asignaciones,
                'message' => 'Todas las asignaciones obtenidas exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener todas las asignaciones.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/api/asignaciones/trashed",
        summary: "Listar solo asignaciones eliminadas (estado = 0)",
        tags: ["Asignaciones de Personal"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function trashed()
    {
        try {

            $asignaciones = Asignacion::where('estado', 0)->with(['trabajoMantenimiento', 'personal'])->get();

            return response()->json([
                'success' => true,
                'data' => $asignaciones,
                'message' => 'Asignaciones eliminadas obtenidas exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones eliminadas.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Delete(
        path: "/api/asignaciones/{id}/force",
        summary: "Eliminar asignación físicamente de la base de datos",
        tags: ["Asignaciones de Personal"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Eliminado físicamente correctamente")
        ]
    )]
    public function forceDelete($id)
    {
        try {

            $asignacion = Asignacion::find($id);

            if (!$asignacion) {

                return response()->json([
                    'success' => false,
                    'message' => 'Asignación no encontrada.'
                ], 404);

            }

            $asignacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Asignación eliminada físicamente de la base de datos.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar físicamente la asignación.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/api/asignaciones/trabajo/{idTrabajo}",
        summary: "Obtener asignaciones por trabajo de mantenimiento",
        tags: ["Asignaciones de Personal"],
        parameters: [
            new OA\Parameter(name: "idTrabajo", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
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

            $asignaciones = Asignacion::where('id_trabajo_mantenimiento', $idTrabajo)
                ->with(['trabajoMantenimiento', 'personal'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $asignaciones,
                'message' => 'Asignaciones del trabajo obtenidas exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones por trabajo.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/api/asignaciones/personal/{idPersonal}",
        summary: "Obtener asignaciones por ID de personal",
        tags: ["Asignaciones de Personal"],
        parameters: [
            new OA\Parameter(name: "idPersonal", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function byPersonal($idPersonal)
    {
        try {

            $personal = Personal::find($idPersonal);

            if (!$personal) {

                return response()->json([
                    'success' => false,
                    'message' => 'Personal no encontrado.'
                ], 404);

            }

            $asignaciones = Asignacion::where('id_personal', $idPersonal)
                ->with(['trabajoMantenimiento', 'personal'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $asignaciones,
                'message' => 'Asignaciones del personal obtenidas exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las asignaciones por personal.',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}
