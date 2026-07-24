<?php

namespace App\Http\Controllers\Api;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use App\Models\CategoriaHerramienta;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class CategoriaHerramientaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categorias-herramientas",
     *     summary="Listar categorías activas",
     *     tags={"Categorías de Herramientas"},
     *     @OA\Response(response=200, description="Operación exitosa")
     * )
     */
    public function index()
    {
        try {
            // Solo obtener categorías activas (estado = 1)
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
/**
     * @OA\Post(
     *     path="/api/categorias-herramientas",
     *     summary="Crear nueva categoría",
     *     tags={"Categorías de Herramientas"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string"),
     *             @OA\Property(property="requiere_certificacion", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Creado exitosamente")
     * )
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'nombre' => 'required|unique:categorias_herramientas,nombre',
                'requiere_certificacion' => 'sometimes|boolean',
                'estado' => 'sometimes|boolean'
            ]);

            // Si no se envía estado, por defecto será 1 (activo)
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
/**
     * @OA\Get(
     *     path="/api/categorias-herramientas/{id}",
     *     summary="Obtener categoría por ID",
     *     tags={"Categorías de Herramientas"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Operación exitosa"),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($id)
    {
        try {

            // Buscar solo si está activa (estado = 1)
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
/**
     * @OA\Put(
     *     path="/api/categorias-herramientas/{id}",
     *     summary="Actualizar categoría",
     *     tags={"Categorías de Herramientas"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string"),
     *             @OA\Property(property="requiere_certificacion", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Actualizado exitosamente")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
              DB::beginTransaction();
            // Buscar solo si está activa (estado = 1)
            $categoria = CategoriaHerramienta::where('estado', 1)->find($id);

            if (!$categoria) {

                return response()->json([
                    'success' => false,
                    'message' => 'Categoría de herramienta no encontrada.'
                ], 404);

            }
            DB::commit();
            $request->validate([
                'nombre' => 'sometimes|unique:categorias_herramientas,nombre,' . $id . ',id_categoria_herramienta',
                'requiere_certificacion' => 'sometimes|boolean',
                'estado' => 'sometimes|boolean'
            ]);

            $categoria->update($request->all());

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
/**
     * @OA\Delete(
     *     path="/api/categorias-herramientas/{id}",
     *     summary="Eliminar categoría (Soft Delete)",
     *     tags={"Categorías de Herramientas"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Eliminado correctamente")
     * )
     */
    // ELIMINADO LÓGICO - Cambiar estado a 0
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            // Buscar solo si está activa (estado = 1)
            $categoria = CategoriaHerramienta::where('estado', 1)->find($id);

            if (!$categoria) {

                return response()->json([
                    'success' => false,
                    'message' => 'Categoría de herramienta no encontrada.'
                ], 404);

            }

            // Eliminado lógico: cambiar estado a 0
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

    // === MÉTODOS NUEVOS PARA EL ELIMINADO LÓGICO CON ESTADO ===

    // Restaurar categoría (cambiar estado a 1)
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            // Buscar la categoría que esté eliminada (estado = 0)
            $categoria = CategoriaHerramienta::where('estado', 0)->find($id);

            if (!$categoria) {

                return response()->json([
                    'success' => false,
                    'message' => 'Categoría de herramienta no encontrada o no está eliminada.'
                ], 404);

            }

            // Restaurar: cambiar estado a 1
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

    // Obtener todas las categorías (incluyendo eliminadas)
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

    // Obtener solo categorías eliminadas (estado = 0)
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

    // Eliminación física (realmente eliminar de la BD)
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

            // Eliminación física (real)
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
