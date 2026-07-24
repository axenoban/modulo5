
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrabajoHerramienta;
use App\Models\TrabajoMantenimiento;
use App\Models\Herramienta;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class TrabajoHerramientaController extends Controller
{
    #[OA\Get(
        path: "/api/trabajo-herramientas",
        summary: "Listar uso de herramientas",
        tags: ["Uso de Herramientas"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => TrabajoHerramienta::where('estado', 1)->with(['trabajoMantenimiento', 'herramienta'])->get()
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los trabajo-herramientas.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Post(
        path: "/api/trabajo-herramientas",
        summary: "Registrar herramienta a un trabajo",
        tags: ["Uso de Herramientas"],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "id_trabajo_mantenimiento", type: "integer"),
                    new OA\Property(property: "id_herramienta", type: "integer"),
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
                'id_herramienta' => 'required|exists:herramientas,id_herramienta',
                'observacion' => 'nullable|string|max:255',
                'estado' => 'sometimes|boolean'
            ]);

            $data = $request->all();
            if (!isset($data['estado'])) {
                $data['estado'] = 1;
            }

            $trabajoHerramienta = TrabajoHerramienta::create($data);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Trabajo-Herramienta registrado correctamente.',
                'data' => $trabajoHerramienta->load(['trabajoMantenimiento', 'herramienta'])
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el trabajo-herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/api/trabajo-herramientas/{id}",
        summary: "Ver registro de herramienta por ID",
        tags: ["Uso de Herramientas"],
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

            $trabajoHerramienta = TrabajoHerramienta::with(['trabajoMantenimiento', 'herramienta'])->find($id);

            if (!$trabajoHerramienta) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo-Herramienta no encontrado.'
                ], 404);

            }

            return response()->json([
                'success' => true,
                'data' => $trabajoHerramienta
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el trabajo-herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Put(
        path: "/api/trabajo-herramientas/{id}",
        summary: "Actualizar registro de herramienta",
        tags: ["Uso de Herramientas"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
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
            $trabajoHerramienta = TrabajoHerramienta::where('estado', 1)->find($id);

            if (!$trabajoHerramienta) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo-Herramienta no encontrado.'
                ], 404);

            }

            $request->validate([
                'id_trabajo_mantenimiento' => 'sometimes|exists:trabajos_mantenimiento,id_trabajo_mantenimiento',
                'id_herramienta' => 'sometimes|exists:herramientas,id_herramienta',
                'observacion' => 'nullable|string|max:255',
                'estado' => 'sometimes|boolean'
            ]);

            $trabajoHerramienta->update($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Trabajo-Herramienta actualizado correctamente.',
                'data' => $trabajoHerramienta->load(['trabajoMantenimiento', 'herramienta'])
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el trabajo-herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Delete(
        path: "/api/trabajo-herramientas/{id}",
        summary: "Remover registro de herramienta (Soft Delete)",
        tags: ["Uso de Herramientas"],
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
            $trabajoHerramienta = TrabajoHerramienta::where('estado', 1)->find($id);

            if (!$trabajoHerramienta) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo-Herramienta no encontrado.'
                ], 404);

            }

            $trabajoHerramienta->update(['estado' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Trabajo-Herramienta eliminado correctamente (estado = 0).'
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el trabajo-herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();
            $trabajoHerramienta = TrabajoHerramienta::where('estado', 0)->find($id);

            if (!$trabajoHerramienta) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo-Herramienta no encontrado o no está eliminado.'
                ], 404);

            }

            $trabajoHerramienta->update(['estado' => 1]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Trabajo-Herramienta restaurado correctamente.',
                'data' => $trabajoHerramienta->load(['trabajoMantenimiento', 'herramienta'])
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar el trabajo-herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function allWithDeleted()
    {
        try {

            $trabajoHerramientas = TrabajoHerramienta::with(['trabajoMantenimiento', 'herramienta'])->get();

            return response()->json([
                'success' => true,
                'data' => $trabajoHerramientas,
                'message' => 'Todos los trabajo-herramientas obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener todos los trabajo-herramientas.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function trashed()
    {
        try {

            $trabajoHerramientas = TrabajoHerramienta::where('estado', 0)
                ->with(['trabajoMantenimiento', 'herramienta'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trabajoHerramientas,
                'message' => 'Trabajo-herramientas eliminados obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los trabajo-herramientas eliminados.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function forceDelete($id)
    {
        try {

            $trabajoHerramienta = TrabajoHerramienta::find($id);

            if (!$trabajoHerramienta) {

                return response()->json([
                    'success' => false,
                    'message' => 'Trabajo-Herramienta no encontrado.'
                ], 404);

            }

            $trabajoHerramienta->delete();

            return response()->json([
                'success' => true,
                'message' => 'Trabajo-Herramienta eliminado físicamente de la base de datos.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar físicamente el trabajo-herramienta.',
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

            $trabajoHerramientas = TrabajoHerramienta::where('id_trabajo_mantenimiento', $idTrabajo)
                ->with(['trabajoMantenimiento', 'herramienta'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trabajoHerramientas,
                'message' => 'Herramientas del trabajo obtenidas exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las herramientas por trabajo.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function byHerramienta($idHerramienta)
    {
        try {

            $herramienta = Herramienta::where('estado', 1)->find($idHerramienta);

            if (!$herramienta) {

                return response()->json([
                    'success' => false,
                    'message' => 'Herramienta no encontrada.'
                ], 404);

            }

            $trabajoHerramientas = TrabajoHerramienta::where('id_herramienta', $idHerramienta)
                ->with(['trabajoMantenimiento', 'herramienta'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trabajoHerramientas,
                'message' => 'Trabajos de la herramienta obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los trabajos por herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}
