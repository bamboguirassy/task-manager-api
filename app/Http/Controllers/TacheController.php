<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use App\Utils\CustomResponse;
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
        $taches = Tache::paginate(10);
        $taches->makeHidden(['created_at', 'updated_at']);
        return CustomResponse::success($taches);
    }

    /**
     * Display current user's taches.
     */
    public function myTaches() {
        $taches = auth()->user()->taches()->paginate(10);
        $taches->makeHidden(['created_at', 'updated_at']);
        return CustomResponse::success($taches);
    }

    /**
     * Store multiple resources in storage.
     */
    public function storeMultiple(Request $request)
    {
        $validators = validator($request->all(), [
            'taches' => 'required|array',
            'taches.*.nom' => 'required|string',
            'taches.*.description' => 'nullable|string',
            'taches.*.priorite' => 'nullable|integer|min:1|max:3',
            'taches.*.date_limite' => 'nullable|date|after:now',
        ]);
        if ($validators->fails()) {
            return CustomResponse::validationError($validators);
        }
        $taches = [];
        try {
            foreach ($request->taches as $tache) {
                $tache = new Tache($tache);
                $tache->saveOrFail();
                $taches[] = $tache;
            }
        } catch (Throwable $th) {
            return CustomResponse::catchException($th);
        }
        return CustomResponse::success($taches, 'Tâches créées avec succès', 201);
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
            return CustomResponse::validationError($validators);
        }
        $tache = new Tache($request->only([
            'nom',
            'description',
            'priorite',
            'date_limite',
        ]));
        try {
            $tache->saveOrFail();
        } catch (Throwable $th) {
            return CustomResponse::catchException($th);
        }
        return CustomResponse::success($tache, 'Tâche créée avec succès', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tache $tache)
    {
        $tache->load('user');
        return CustomResponse::success($tache);
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
            return CustomResponse::validationError($validators);
        }
        try {
            $tache->update($request->only([
                'nom',
                'description',
                'priorite',
                'date_limite',
            ]));
        } catch (Throwable $th) {
            return CustomResponse::catchException($th);
        }
        return CustomResponse::success($tache, 'Tâche modifiée avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uid)
    {
        $tache = Tache::where('uid', $uid)->first();
        if (!$tache) {
            return CustomResponse::error('Tâche introuvable', 404);
        }
        try {
            $tache->delete();
        } catch (Throwable $th) {
            return CustomResponse::catchException($th);
        }
        return CustomResponse::success(null, 'Tâche supprimée avec succès');
    }

    public function terminer($uid)
    {
        $tache = Tache::where('uid', $uid)->first();
        if (!$tache) {
            return CustomResponse::error('Tâche introuvable', 404);
        }
        if ($tache->terminee) {
            return CustomResponse::error('Impossible de terminer une tâche déjà terminée !', 400);
        }
        $tache->terminee = true;
        $tache->save();
        return CustomResponse::success($tache, 'Tâche terminée avec succès');
    }
}
