<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function discharge(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        $doctor = $request->user()->doctorProfile;

        $appointment->update([
            'doctor_id' => $doctor->id,
            'status' => 'completed',
        ]);

        Invoice::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'patient_id' => $appointment->patient_id,
            'amount' => $data['amount'],
            'description' => $data['description'],
            'status' => 'unpaid',
        ]);

        return redirect()->route('doctor.appointments.index')->with('success', 'Patient discharged and invoice created.');
    }

    public function patientInvoices(Request $request)
    {
        $patient = $request->user()->patientProfile;
        $invoices = Invoice::with('appointment')->where('patient_id', $patient->id)->latest()->get();

        return view('patient.invoices.index', compact('invoices'));
    }
}
