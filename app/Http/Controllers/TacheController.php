<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class TacheController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taches = Tache::paginate(4);
        $taches->makeHidden(['created_at', 'updated_at']);
        return response()->json([
            'error' => false,
            'taches' => $taches
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validators = validator($request->all(), [
            'nom' => 'required|string',
            'description' => 'nullable|string',
            'priorite' => 'nullable|integer|min:1|max:3',
            'date_limite' => 'nullable|date|after:now',
        ]);
        if ($validators->fails()) {
            return response()->json([
                'error' => true,
                'validation' => true,
                'errors' => $validators->errors()
            ], 400);
        }
        $tache = new Tache($request->only([
            'nom',
            'description',
            'priorite',
            'date_limite',
        ]));
        $tache->uid = Str::uuid();
        try {
            $tache->saveOrFail();
        } catch (Throwable $th) {
            return response()->json([
                'error' => true,
                'validation' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        return response()->json([
            'error' => false,
            'validation' => false,
            'message' => 'Tâche créée avec succès',
            'tache' => $tache
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tache $tache)
    {
        return response()->json([
            'error' => false,
            'tache' => $tache
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tache $tache)
    {
        $validators = validator($request->all(), [
            'nom' => 'string',
            'description' => 'string|nullable',
            'priorite' => 'integer|min:1|max:3',
            'date_limite' => 'date',
        ]);
        if ($validators->fails()) {
            return response()->json([
                'error' => true,
                'validation' => true,
                'errors' => $validators->errors()
            ], 400);
        }
        try {
            $tache->update($request->only([
                'nom',
                'description',
                'priorite',
                'date_limite',
            ]));
        } catch (Throwable $th) {
            return response()->json([
                'error' => true,
                'validation' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        return response()->json([
            'error' => false,
            'validation' => false,
            'message' => 'Tâche modifiée avec succès',
            'tache' => $tache
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tache $tache)
    {
        try {
            $tache->delete();
        } catch (Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ], 500);
        }
        return response()->json([
            'error' => false,
            'message' => 'Tâche supprimée avec succès',
        ], 200);
    }

    public function terminer(Tache $tache)
    {
        if ($tache->terminee) {
            return response()->json([
                'error' => true,
                'message' => 'On ne peut pas terminer une tâche déjà terminée',
            ], 400);
        }
        $tache->terminee = true;
        $tache->save();
        return response()->json([
            'error' => false,
            'message' => 'Tâche terminée avec succès',
            'tache' => $tache
        ], 200);
    }
}
