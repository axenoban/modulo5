<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Herramienta;
use App\Models\CategoriaHerramienta;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class HerramientaController extends Controller
{
    #[OA\Get(
        path: "/herramientas",
        summary: "Listar herramientas activas",
        tags: ["Herramientas"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function index()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => Herramienta::where('estado', 1)->with('categoria')->get()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las herramientas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Post(
        path: "/herramientas",
        summary: "Registrar nueva herramienta",
        tags: ["Herramientas"],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "id_categoria_herramienta", type: "integer"),
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "nro_serie_interno", type: "string"),
                    new OA\Property(property: "estado_fisico", type: "string", enum: ["excelente", "bueno", "regular", "malo"])
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
                'id_categoria_herramienta' => 'required|exists:categorias_herramientas,id_categoria_herramienta',
                'nombre' => 'required|string|max:100',
                'nro_serie_interno' => 'required|string|max:100|unique:herramientas,nro_serie_interno',
                'estado_fisico' => 'required|in:excelente,bueno,regular,malo',
                'estado' => 'sometimes|boolean'
            ]);

            $data = $request->all();
            if (!isset($data['estado'])) {
                $data['estado'] = 1;
            }

            $herramienta = Herramienta::create($data);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Herramienta registrada correctamente.',
                'data' => $herramienta->load('categoria')
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la herramienta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/herramientas/{id}",
        summary: "Obtener herramienta por ID",
        tags: ["Herramientas"],
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
            $herramienta = Herramienta::where('estado', 1)->with('categoria')->find($id);

            if (!$herramienta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Herramienta no encontrada.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $herramienta
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar la herramienta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Put(
        path: "/herramientas/{id}",
        summary: "Actualizar herramienta",
        tags: ["Herramientas"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "id_categoria_herramienta", type: "integer"),
                    new OA\Property(property: "nombre", type: "string"),
                    new OA\Property(property: "nro_serie_interno", type: "string"),
                    new OA\Property(property: "estado_fisico", type: "string", enum: ["excelente", "bueno", "regular", "malo"])
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
            $herramienta = Herramienta::where('estado', 1)->find($id);

            if (!$herramienta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Herramienta no encontrada.'
                ], 404);
            }

            $request->validate([
                'id_categoria_herramienta' => 'sometimes|exists:categorias_herramientas,id_categoria_herramienta',
                'nombre' => 'sometimes|string|max:100',
                'nro_serie_interno' => 'sometimes|string|max:100|unique:herramientas,nro_serie_interno,' . $id . ',id_herramienta',
                'estado_fisico' => 'sometimes|in:excelente,bueno,regular,malo',
                'estado' => 'sometimes|boolean'
            ]);

            $herramienta->update($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Herramienta actualizada correctamente.',
                'data' => $herramienta->load('categoria')
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la herramienta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Delete(
        path: "/herramientas/{id}",
        summary: "Desactivar herramienta (Soft Delete)",
        tags: ["Herramientas"],
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

            $herramienta = Herramienta::where('estado', 1)->find($id);

            if (!$herramienta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Herramienta no encontrada.'
                ], 404);
            }

            $herramienta->update(['estado' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Herramienta eliminada correctamente (estado = 0).'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la herramienta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Patch(
        path: "/herramientas/{id}/restore",
        summary: "Restaurar herramienta eliminada lógicamente",
        tags: ["Herramientas"],
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
            $herramienta = Herramienta::where('estado', 0)->find($id);

            if (!$herramienta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Herramienta no encontrada o no está eliminada.'
                ], 404);
            }

            $herramienta->update(['estado' => 1]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Herramienta restaurada correctamente (estado = 1).',
                'data' => $herramienta->load('categoria')
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar la herramienta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/herramientas/all-with-deleted",
        summary: "Obtener todas las herramientas (incluyendo eliminadas)",
        tags: ["Herramientas"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function allWithDeleted()
    {
        try {
            $herramientas = Herramienta::with('categoria')->get();

            return response()->json([
                'success' => true,
                'data' => $herramientas,
                'message' => 'Todas las herramientas obtenidas exitosamente (incluye eliminadas).'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener todas las herramientas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/herramientas/trashed",
        summary: "Obtener solo herramientas eliminadas (estado = 0)",
        tags: ["Herramientas"],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function trashed()
    {
        try {
            $herramientas = Herramienta::where('estado', 0)->with('categoria')->get();

            return response()->json([
                'success' => true,
                'data' => $herramientas,
                'message' => 'Herramientas eliminadas obtenidas exitosamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las herramientas eliminadas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/herramientas/categoria/{idCategoria}",
        summary: "Obtener herramientas por categoría",
        tags: ["Herramientas"],
        parameters: [
            new OA\Parameter(name: "idCategoria", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function byCategoria($idCategoria)
    {
        try {
            $categoria = CategoriaHerramienta::where('estado', 1)->find($idCategoria);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoría no encontrada.'
                ], 404);
            }

            $herramientas = Herramienta::where('estado', 1)
                ->where('id_categoria_herramienta', $idCategoria)
                ->with('categoria')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $herramientas,
                'message' => 'Herramientas de la categoría obtenidas exitosamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las herramientas por categoría.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Get(
        path: "/herramientas/estado-fisico/{estadoFisico}",
        summary: "Obtener herramientas por estado físico",
        tags: ["Herramientas"],
        parameters: [
            new OA\Parameter(
                name: "estadoFisico",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", enum: ["excelente", "bueno", "regular", "malo"])
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Operación exitosa")
        ]
    )]
    public function byEstadoFisico($estadoFisico)
    {
        try {
            $estadosValidos = ['excelente', 'bueno', 'regular', 'malo'];

            if (!in_array($estadoFisico, $estadosValidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estado físico no válido. Debe ser: excelente, bueno, regular o malo.'
                ], 400);
            }

            $herramientas = Herramienta::where('estado', 1)
                ->where('estado_fisico', $estadoFisico)
                ->with('categoria')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $herramientas,
                'message' => 'Herramientas con estado físico ' . $estadoFisico . ' obtenidas exitosamente.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las herramientas por estado físico.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Delete(
        path: "/herramientas/{id}/force",
        summary: "Eliminar herramienta físicamente de la base de datos",
        tags: ["Herramientas"],
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
            $herramienta = Herramienta::find($id);

            if (!$herramienta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Herramienta no encontrada.'
                ], 404);
            }

            $herramienta->delete();

            return response()->json([
                'success' => true,
                'message' => 'Herramienta eliminada físicamente de la base de datos.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar físicamente la herramienta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
