<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'no_rekam_medik' => 'required|string'
        ]);

        $patient = Patient::where('no_rekam_medik', $request->no_rekam_medik)->first();

        if (!$patient) {
            return response()->json([
                "message" => "Pasien tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Data pasien ditemukan",
            "data" => $patient
        ], 200);
    }

    public function addVisitor(Request $request)
    {
        $request->validate([
            'no_rekam_medik' => 'required|string'
        ]);

        $patient = Patient::where('no_rekam_medik', $request->no_rekam_medik)->first();

        if (!$patient) {
            return response()->json([
                "message" => "Pasien tidak ditemukan"
            ], 404);
        }

        $patient->jml_kunjungan += 1;
        $patient->save();

        return response()->json([
            "message" => "Kunjungan berhasil ditambahkan",
            "data" => $patient
        ], 200);
    }
}
