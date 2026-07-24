<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mantenimiento;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class MantenimientoController extends Controller
{
    #[OA\Get(
        path: "/api/mantenimientos",
        summary: "Listar tipos de mantenimiento activos",
        tags: ["Tipos de Mantenimiento"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => Mantenimiento::where('estado', 1)->get()
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los mantenimientos.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Post(
        path: "/api/mantenimientos",
        summary: "Crear tipo de mantenimiento",
        tags: ["Tipos de Mantenimiento"],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "descripcion", type: "string"),
                    new OA\Property(property: "tarifa_base", type: "number", format: "float"),
                    new OA\Property(property: "tiempo_estimado", type: "integer")
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
                'nombre' => 'required|unique:mantenimientos,nombre',
                'descripcion' => 'nullable|string',
                'tarifa_base' => 'sometimes|numeric|min:0',
                'tiempo_estimado' => 'nullable|integer|min:1',
                'estado' => 'sometimes|boolean'
            ]);

            $data = $request->all();
            if (!isset($data['estado'])) {
                $data['estado'] = 1;
            }

            $mantenimiento = Mantenimiento::create($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento registrado correctamente.',
                'data' => $mantenimiento
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/api/mantenimientos/{id}",
        summary: "Obtener mantenimiento por ID",
        tags: ["Tipos de Mantenimiento"],
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
            
            $mantenimiento = Mantenimiento::where('estado', 1)->find($id);

            if (!$mantenimiento) {

                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado.'
                ], 404);

            }

            return response()->json([
                'success' => true,
                'data' => $mantenimiento
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Put(
        path: "/api/mantenimientos/{id}",
        summary: "Actualizar mantenimiento",
        tags: ["Tipos de Mantenimiento"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "tarifa_base", type: "number", format: "float"),
                    new OA\Property(property: "tiempo_estimado", type: "integer")
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
            $mantenimiento = Mantenimiento::where('estado', 1)->find($id);

            if (!$mantenimiento) {

                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado.'
                ], 404);

            }
            
            $request->validate([
                'nombre' => 'sometimes|unique:mantenimientos,nombre,' . $id . ',id_mantenimiento',
                'descripcion' => 'nullable|string',
                'tarifa_base' => 'sometimes|numeric|min:0',
                'tiempo_estimado' => 'nullable|integer|min:1',
                'estado' => 'sometimes|boolean'
            ]);

            $mantenimiento->update($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento actualizado correctamente.',
                'data' => $mantenimiento
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Delete(
        path: "/api/mantenimientos/{id}",
        summary: "Eliminar mantenimiento (Soft Delete)",
        tags: ["Tipos de Mantenimiento"],
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
            $mantenimiento = Mantenimiento::where('estado', 1)->find($id);

            if (!$mantenimiento) {

                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado.'
                ], 404);

            }

            $mantenimiento->update(['estado' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento eliminado correctamente (estado = 0).'
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();
            $mantenimiento = Mantenimiento::where('estado', 0)->find($id);

            if (!$mantenimiento) {

                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado o no está eliminado.'
                ], 404);

            }

            $mantenimiento->update(['estado' => 1]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento restaurado correctamente.',
                'data' => $mantenimiento
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar el mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function allWithDeleted()
    {
        try {

            $mantenimientos = Mantenimiento::all();

            return response()->json([
                'success' => true,
                'data' => $mantenimientos,
                'message' => 'Todos los mantenimientos obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener todos los mantenimientos.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function trashed()
    {
        try {

            $mantenimientos = Mantenimiento::where('estado', 0)->get();

            return response()->json([
                'success' => true,
                'data' => $mantenimientos,
                'message' => 'Mantenimientos eliminados obtenidos exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los mantenimientos eliminados.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    public function forceDelete($id)
    {
        try {

            $mantenimiento = Mantenimiento::find($id);

            if (!$mantenimiento) {

                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado.'
                ], 404);

            }

            $mantenimiento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento eliminado físicamente de la base de datos.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar físicamente el mantenimiento.',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}
