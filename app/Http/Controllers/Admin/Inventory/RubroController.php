<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Rubro;
use App\Models\RubroField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RubroController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('all')) {
            return Rubro::with('category')->where('estado', 'activo')->get();
        }
        
        if ($request->has('category_id')) {
             return Rubro::where('category_id', $request->category_id)->where('estado', 'activo')->get();
        }

        $perPage = $request->input('per_page', 25);
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        return Rubro::with('category')
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
            'fields' => 'nullable|array',
            'fields.*.nombre' => 'required|string',
            'fields.*.label' => 'required|string',
            'fields.*.tipo' => 'required|in:text,number,select,boolean',
            'fields.*.opciones' => 'nullable|array',
            'fields.*.requerido' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $rubro = Rubro::create([
                'category_id' => $validated['category_id'],
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'estado' => $validated['estado'],
            ]);

            if (!empty($validated['fields'])) {
                foreach ($validated['fields'] as $fieldData) {
                    $rubro->fields()->create($fieldData);
                }
            }

            DB::commit();
            return response()->json($rubro->load('fields'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Rubro $rubro)
    {
        return $rubro->load(['category', 'fields']);
    }

    public function update(Request $request, Rubro $rubro)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
            'fields' => 'nullable|array',
            'fields.*.id' => 'nullable|exists:rubro_fields,id', // ID is null for new fields
            'fields.*.nombre' => 'required|string',
            'fields.*.label' => 'required|string',
            'fields.*.tipo' => 'required|in:text,number,select,boolean',
            'fields.*.opciones' => 'nullable|array',
            'fields.*.requerido' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $rubro->update([
                'category_id' => $validated['category_id'],
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'estado' => $validated['estado'],
            ]);

            // Handle Fields
            if (isset($validated['fields'])) {
                // Get IDs of fields to keep
                $keepIds = array_filter(array_column($validated['fields'], 'id'));
                
                // Delete fields not in the list
                $rubro->fields()->whereNotIn('id', $keepIds)->delete();

                foreach ($validated['fields'] as $fieldData) {
                    if (isset($fieldData['id'])) {
                        // Update existing
                        $field = RubroField::find($fieldData['id']);
                        $field->update($fieldData);
                    } else {
                        // Create new
                        $rubro->fields()->create($fieldData);
                    }
                }
            }

            DB::commit();
            return response()->json($rubro->load('fields'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Rubro $rubro)
    {
        $rubro->delete();
        return response()->json(null, 204);
    }
}
