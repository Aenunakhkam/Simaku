<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\PaymentCategory;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $query = Billing::with(['student.classroom', 'category', 'academicYear'])
            ->where('academic_year_id', session('academic_year_id'))
            ->latest();

        if ($request->search) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('nisn', 'like', "%{$request->search}%");
            });
        }

        $billings = $query->paginate($request->per_page ?? 10)->withQueryString();
        
        $classrooms = Classroom::all();
        $students = Student::with('classroom')->where('status', 'active')->get();
        $categories = PaymentCategory::all();
        $academicYears = AcademicYear::all();

        return Inertia::render('Billings/Index', [
            'billings' => $billings,
            'classrooms' => $classrooms,
            'students' => $students,
            'categories' => $categories,
            'academicYears' => $academicYears,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_type' => 'required|in:classroom,student',
            'target_id' => 'required|integer',
            'payment_category_ids' => 'required|array|min:1',
            'payment_category_ids.*' => 'exists:payment_categories,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $studentsToBill = collect();

        if ($validated['target_type'] === 'classroom') {
            $studentsToBill = Student::where('classroom_id', $validated['target_id'])
                                     ->where('status', 'active')
                                     ->get();
        } else {
            $student = Student::find($validated['target_id']);
            if ($student) $studentsToBill->push($student);
        }

        $count = 0;
        
        // Fetch all selected categories so we know their default amounts
        $categories = PaymentCategory::whereIn('id', $validated['payment_category_ids'])->get()->keyBy('id');

        foreach ($studentsToBill as $student) {
            foreach ($validated['payment_category_ids'] as $categoryId) {
                // Check if billing already exists to prevent duplicate
                $exists = Billing::where('student_id', $student->id)
                    ->where('payment_category_id', $categoryId)
                    ->where('academic_year_id', $validated['academic_year_id'])
                    ->where('month', $validated['month'])
                    ->exists();

                if (!$exists && isset($categories[$categoryId])) {
                    Billing::create([
                        'student_id' => $student->id,
                        'payment_category_id' => $categoryId,
                        'academic_year_id' => $validated['academic_year_id'],
                        'month' => $validated['month'],
                        'amount' => $categories[$categoryId]->default_amount,
                        'is_paid' => false,
                    ]);
                    $count++;
                }
            }
        }

        return redirect()->back()->with('message', "$count Tagihan berhasil di-generate.");
    }
    
    public function destroy(Billing $billing)
    {
        if ($billing->paid_amount > 0) {
            return redirect()->back()->with('error', 'Tagihan ini tidak dapat dihapus karena sudah ada pembayaran yang masuk.');
        }
        
        $billing->delete();
        return redirect()->back()->with('message', 'Tagihan berhasil dihapus.');
    }
}
