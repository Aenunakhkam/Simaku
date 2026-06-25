<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$academicYearId = \App\Models\AcademicYear::latest('id')->first()->id;

$totalStudents = \App\Models\Student::where('status', 'active')->count();
$totalBillingAmount = \App\Models\Billing::where('academic_year_id', $academicYearId)->sum('amount');
$totalPaid = \App\Models\PaymentDetail::whereHas('billing', function ($query) use ($academicYearId) {
    $query->where('academic_year_id', $academicYearId);
})->sum('amount');
$totalTunggakan = max(0, $totalBillingAmount - $totalPaid);

echo json_encode([
    'total_students' => $totalStudents,
    'total_billing_amount' => $totalBillingAmount,
    'total_paid' => $totalPaid,
    'total_tunggakan' => $totalTunggakan,
]);
