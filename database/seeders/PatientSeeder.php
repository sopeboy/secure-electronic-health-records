<?php

namespace Database\Seeders;

use App\Models\LabResult;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Vital;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::where('email', 'admin@sehrs.test')->firstOrFail();
        $doctor  = User::where('email', 'doctor@sehrs.test')->firstOrFail();
        $doctor2 = User::where('email', 'doctor2@sehrs.test')->firstOrFail();
        $nurse   = User::where('email', 'nurse@sehrs.test')->firstOrFail();
        $lab     = User::where('email', 'lab@sehrs.test')->firstOrFail();

        $diagnoses = ['Malaria', 'Typhoid fever', 'Hypertension', 'Peptic ulcer', 'Upper respiratory tract infection'];
        $drugs     = [['Artemether/Lumefantrine', '4 tablets twice daily for 3 days'],
                      ['Paracetamol', '1g every 8 hours for 3 days'],
                      ['Amlodipine', '5mg once daily'],
                      ['Omeprazole', '20mg once daily before breakfast']];
        $tests     = [['Malaria parasite (MP)', 'Positive (+)'], ['Full blood count', 'Within normal range'],
                      ['Widal test', 'Negative'], ['Fasting blood sugar', '5.2 mmol/L']];

        $patients = Patient::factory()->count(20)->create(['registered_by' => $admin->id]);

        foreach ($patients as $i => $patient) {
            // First 10 patients: doctor 1 + nurse. Next 10: doctor 2 only.
            // This lets you test "doctor cannot open an unassigned patient".
            $i < 10
                ? $patient->assignedStaff()->attach([$doctor->id, $nurse->id])
                : $patient->assignedStaff()->attach([$doctor2->id]);

            $treatingDoctor = $i < 10 ? $doctor : $doctor2;

            $record = new MedicalRecord(['diagnosis' => $diagnoses[array_rand($diagnoses)], 'notes' => 'Synthetic test record.']);
            $record->patient_id = $patient->id;
            $record->created_by = $treatingDoctor->id;
            $record->save();

            [$medication, $dosage] = $drugs[array_rand($drugs)];
            $rx = new Prescription(['medication' => $medication, 'dosage' => $dosage, 'instructions' => 'Take after meals.']);
            $rx->patient_id = $patient->id;
            $rx->prescribed_by = $treatingDoctor->id;
            $rx->save();

            $vital = new Vital([
                'temperature'    => fake()->randomFloat(1, 36.0, 39.5),
                'blood_pressure' => fake()->numberBetween(100, 150) . '/' . fake()->numberBetween(60, 95),
                'pulse'          => fake()->numberBetween(60, 110),
                'weight'         => fake()->randomFloat(1, 10, 95),
            ]);
            $vital->patient_id = $patient->id;
            $vital->recorded_by = $nurse->id;
            $vital->save();

            [$testName, $result] = $tests[array_rand($tests)];
            $lr = new LabResult(['test_name' => $testName, 'result' => $result]);
            $lr->patient_id = $patient->id;
            $lr->recorded_by = $lab->id;
            $lr->source = 'staff';
            $lr->save();
        }
    }
}
