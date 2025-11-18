<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $patients = Patient::all();

            return response()->json([
                'message' => 'Data pasien berhasil diambil',
                'data' => $patients,
                'success' => true
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data pasien',
                'error' => $e->getMessage(),
                'success' => false
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'nama' => ['required', 'string', 'max:255'],
                'nik' => ['required', 'string', 'max:16', 'unique:patients,nik'],
                'alamat' => ['required', 'string', 'max:500'],
            ]);

            // Generate RM otomatis
            $lastPatient = Patient::orderBy('id', 'desc')->first();
            if (!$lastPatient) {
                $nextNumber = 1;
            } else {
                $lastRm = intval(substr($lastPatient->no_rekam_medik, 2));
                $nextNumber = $lastRm + 1;
            }

            $validated['no_rekam_medik'] = 'RM' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $patient = Patient::create($validated);

            return response()->json([
                'message' => 'Data pasien berhasil disimpan',
                'data' => $patient,
                'success' => true
            ], 201);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
                'success' => false
            ], 422);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data pasien',
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function generateRm()
    {
        $lastPatient = Patient::orderBy('id', 'desc')->first();

        if (!$lastPatient) {
            $nextNumber = 1;
        } else {
            // Ambil angka setelah prefix "RM"
            $lastRm = intval(substr($lastPatient->no_rekam_medik, 2));
            $nextNumber = $lastRm + 1;
        }

        $newRm = 'RM' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'data' => $newRm
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        //
    }
}
