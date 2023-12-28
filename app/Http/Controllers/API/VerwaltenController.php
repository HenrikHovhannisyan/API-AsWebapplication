<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\verwalten;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerwaltenController extends Controller
{
    /**
     * @param verwalten $verwalten
     * @param $points
     */
    private function updatePoints(Verwalten $verwalten, $points)
    {
        $stufe = 1;
        $thresholds = [100, 750, 2500, 6500, 15000, 30000, 50000];

        foreach ($thresholds as $index => $threshold) {
            if ($points < $threshold) {
                $stufe = $index + 1;
                break;
            }
        }

        $verwalten->update([
            'stufe' => $stufe,
            'punkte' => $points,
        ]);
    }

    /**
     * @param verwalten $verwalten
     * @return JsonResponse
     */
    public function show(verwalten $verwalten)
    {
        $punktePercentage = ($verwalten->punkte / 50000) * 100;
        $user = User::where('id', $verwalten->user_id)->first();

        return response()->json([
            "user_id" => $verwalten->user_id,
            "name" => $user->name,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "stufe" => $verwalten->stufe,
            "punkte" => $verwalten->punkte,
            'punkte_percentage' => $punktePercentage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param verwalten $verwalten
     * @return JsonResponse
     */
    public function update(Request $request, Verwalten $verwalten)
    {
        $validator = validator($request->json()->all(), [
            'punkte' => 'required|numeric',
            'stufe' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $points = $verwalten->punkte + $request->json()->all()['punkte'];

        $this->updatePoints($verwalten, $points);

        return response()->json(['message' => 'Verwalten updated successfully']);
    }

    /**
     * @param Request $request
     * @param verwalten $verwalten
     * @return JsonResponse
     */
    public function abziehen(Request $request, Verwalten $verwalten)
    {
        $validator = validator($request->json()->all(), [
            'abziehen_value' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $points = $verwalten->punkte - $request->json()->all()['abziehen_value'];
        $this->updatePoints($verwalten, $points);

        return response()->json(['message' => 'Verwalten updated successfully']);
    }
}
