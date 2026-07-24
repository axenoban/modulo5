<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaHerramienta;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class CategoriaHerramientaController extends Controller
{
    #[OA\Get(
        path: "/categorias-herramientas",
        summary: "Listar categorías activas",
        tags: ["Categorías de Herramientas"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => CategoriaHerramienta::where('estado', 1)->get()
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías de herramientas.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Post(
        path: "/categorias-herramientas",
        summary: "Crear nueva categoría",
        tags: ["Categorías de Herramientas"],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "requiere_certificacion", type: "boolean")
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
                'nombre' => 'required|unique:categorias_herramientas,nombre',
                'requiere_certificacion' => 'sometimes|boolean',
                'estado' => 'sometimes|boolean'
            ]);

            $data = $request->all();
            if (!isset($data['estado'])) {
                $data['estado'] = 1;
            }

            $categoria = CategoriaHerramienta::create($data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Categoría de herramienta registrada correctamente.',
                'data' => $categoria
            ], 201);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la categoría de herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/categorias-herramientas/{id}",
        summary: "Obtener categoría por ID",
        tags: ["Categorías de Herramientas"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa"),
            new OA\Response(response: 404, description: "No encontrado")
        ]
    )]
    public function show($id)
    {
        try {

            $categoria = CategoriaHerramienta::where('estado', 1)->find($id);

            if (!$categoria) {

                return response()->json([
                    'success' => false,
                    'message' => 'Categoría de herramienta no encontrada.'
                ], 404);

            }

            return response()->json([
                'success' => true,
                'data' => $categoria
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al buscar la categoría de herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Put(
        path: "/categorias-herramientas/{id}",
        summary: "Actualizar categoría",
        tags: ["Categorías de Herramientas"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "requiere_certificacion", type: "boolean")
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
            $categoria = CategoriaHerramienta::where('estado', 1)->find($id);

            if (!$categoria) {

                return response()->json([
                    'success' => false,
                    'message' => 'Categoría de herramienta no encontrada.'
                ], 404);

            }

            $request->validate([
                'nombre' => 'sometimes|unique:categorias_herramientas,nombre,' . $id . ',id_categoria_herramienta',
                'requiere_certificacion' => 'sometimes|boolean',
                'estado' => 'sometimes|boolean'
            ]);

            $categoria->update($request->all());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoría de herramienta actualizada correctamente.',
                'data' => $categoria
            ], 200);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría de herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Delete(
        path: "/categorias-herramientas/{id}",
        summary: "Eliminar categoría (Soft Delete)",
        tags: ["Categorías de Herramientas"],
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
            $categoria = CategoriaHerramienta::where('estado', 1)->find($id);

            if (!$categoria) {

                return response()->json([
                    'success' => false,
                    'message' => 'Categoría de herramienta no encontrada.'
                ], 404);

            }

            $categoria->update(['estado' => 0]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoría de herramienta eliminada correctamente (estado = 0).'
            ], 200);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría de herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Patch(
        path: "/categorias-herramientas/{id}/restore",
        summary: "Restaurar categoría eliminada lógicamente",
        tags: ["Categorías de Herramientas"],
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
            $categoria = CategoriaHerramienta::where('estado', 0)->find($id);

            if (!$categoria) {

                return response()->json([
                    'success' => false,
                    'message' => 'Categoría de herramienta no encontrada o no está eliminada.'
                ], 404);

            }

            $categoria->update(['estado' => 1]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoría de herramienta restaurada correctamente (estado = 1).',
                'data' => $categoria
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar la categoría de herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/categorias-herramientas/all-with-deleted",
        summary: "Obtener todas las categorías (incluyendo eliminadas)",
        tags: ["Categorías de Herramientas"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function allWithDeleted()
    {
        try {

            $categorias = CategoriaHerramienta::all();

            return response()->json([
                'success' => true,
                'data' => $categorias,
                'message' => 'Todas las categorías obtenidas exitosamente (incluye eliminadas).'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener todas las categorías.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Get(
        path: "/categorias-herramientas/trashed",
        summary: "Obtener solo categorías eliminadas (estado = 0)",
        tags: ["Categorías de Herramientas"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function trashed()
    {
        try {

            $categorias = CategoriaHerramienta::where('estado', 0)->get();

            return response()->json([
                'success' => true,
                'data' => $categorias,
                'message' => 'Categorías eliminadas obtenidas exitosamente.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías eliminadas.',
                'error' => $e->getMessage()
            ], 500);

        }
    }

    #[OA\Delete(
        path: "/categorias-herramientas/{id}/force",
        summary: "Eliminar categoría físicamente de la base de datos",
        tags: ["Categorías de Herramientas"],
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

            $categoria = CategoriaHerramienta::find($id);

            if (!$categoria) {

                return response()->json([
                    'success' => false,
                    'message' => 'Categoría de herramienta no encontrada.'
                ], 404);

            }

            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría de herramienta eliminada físicamente de la base de datos.'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar físicamente la categoría de herramienta.',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}
