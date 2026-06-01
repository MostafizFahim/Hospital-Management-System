<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function create()
    {
        $doctors = DoctorProfile::with('user')->where('status', 'approved')->get();

        return view('patient.appointments.create', compact('doctors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'doctor_id' => ['nullable', 'exists:doctor_profiles,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'symptoms' => ['required', 'string', 'max:2000'],
        ]);

        $data['patient_id'] = $request->user()->patientProfile->id;
        $data['status'] = 'pending';

        Appointment::create($data);

        return redirect()->route('patient.dashboard')->with('success', 'Appointment booked successfully.');
    }

    public function doctorIndex(Request $request)
    {
        $doctor = $request->user()->doctorProfile;

        $appointments = Appointment::with('patient.user')
            ->where(function ($query) use ($doctor) {
                $query->whereNull('doctor_id')->orWhere('doctor_id', $doctor->id);
            })
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->get();

        return view('doctor.appointments.index', compact('appointments'));
    }
}
