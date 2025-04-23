<?php

namespace App\Services\Student;

use App\Exceptions\EmptyRecordsException;
use App\Exceptions\InvalidValueException;
use App\Models\Promotion;
use App\Models\School;
use App\Models\StudentRecord;
use App\Models\User;
use App\Services\MyClass\MyClassService;
use App\Services\Print\PrintService;
use App\Services\Section\SectionService;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentService
{
    /**
     * Instance of class service.
     *
     * @var MyClassService
     */
    public $myClassService;

    /**
     * Instance of user service.
     *
     * @var UserService
     */
    public $userService;

    /**
     * Instance of section service.
     */
    public SectionService $sectionService;

    public function __construct(MyClassService $myClassService, UserService $userService, SectionService $sectionService)
    {
        $this->myClassService = $myClassService;
        $this->sectionService = $sectionService;
        $this->userService = $userService;
    }

    /**
     * Get all students in school.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllStudents()
    {
        return $this->userService->getUsersByRole('student')->load('studentRecord');
    }

    /**
     * Get all active students in school.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActiveStudents()
    {
        return $this->userService->getUsersByRole('student')->load('studentRecord')->filter(function ($student) {
            if ($student->studentRecord) {
                return $student->studentRecord->is_graduated == false;
            }
            return false;
        });
    }

    /**
     * Get all graduated students in school.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllGraduatedStudents()
    {
        return $this->userService->getUsersByRole('student')->load('studentRecord')->filter(function ($student) {
            if ($student->studentRecord) {
                return $student->studentRecord->withoutGlobalScopes()->where('is_graduated', true)->exists();
            }
            return false;
        });
    }

    /**
     * Get a student by id.
     *
     * @param array|int $id student id
     *
     * @return \App\Models\User
     */
    public function getStudentById($id)
    {
        return $this->userService->getUserById($id)->load('studentRecord');
    }

    /**
     * Create student.
     *
     * @param array $record Array of student record
     *
     * @return \App\Models\User
     */
    public function createStudent($request)
    {
        $data = $request->validated();

        // Generate a default email if not provided
        if (empty($data['email'])) {
            // Create a default email using name and random string
            $firstName = $data['first_name'] ?? '';
            $lastName = $data['last_name'] ?? '';
            $randomString = strtolower(substr(md5(uniqid(mt_rand(), true)), 0, 8));

            // Clean the name parts (remove spaces, special chars)
            $firstName = preg_replace('/[^a-zA-Z0-9]/', '', $firstName);
            $lastName = preg_replace('/[^a-zA-Z0-9]/', '', $lastName);

            // Create the email
            $defaultEmail = strtolower($firstName . '.' . $lastName . '.' . $randomString . '@tamatprimary.edu');

            // Check if this email exists already
            $emailExists = User::where('email', $defaultEmail)->exists();
            if ($emailExists) {
                // Add another random string if email already exists
                $randomString = strtolower(substr(md5(uniqid(mt_rand(), true)), 0, 8));
                $defaultEmail = strtolower($firstName . '.' . $lastName . '.' . $randomString . '@tamatprimary.edu');
            }
        } else {
            $defaultEmail = $data['email'];
        }

        // First create the user
        $user = User::create([
            'name' => $data['first_name'] . ' ' . $data['last_name'] . (isset($data['other_names']) ? ' ' . $data['other_names'] : ''),
            'birthday' => $data['birthday'],
            'gender' => $data['gender'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'],
            'nationality' => $data['nationality'],
            'state' => $data['state'],
            'city' => $data['city'],
            'email' => $defaultEmail,  // Using our generated email when original is null
            'password' => isset($data['password']) ? Hash::make($data['password']) : Hash::make('password123'), // Default password if not provided
            'school_id' => auth()->user()->school_id,
        ]);

        // Calculate age based on birthday
        $age = \Carbon\Carbon::parse($data['birthday'])->age;
        $user->age = $age;
        $user->save();

        // Profile photo handling
        if ($request->hasFile('profile_photo')) {
            $user->profile_photo_path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->save();
        }

        // Handle optional fields
        $admissionNumber = $data['admission_number'] ?? $this->generateAdmissionNumber();

        // Create the student record with user_id
        $studentRecord = StudentRecord::create([
            'user_id' => $user->id,
            'admission_number' => $admissionNumber,
            'admission_date' => $data['admission_date'],
            'my_class_id' => $data['my_class_id'],
            'section_id' => $data['section_id'],
            'father_full_name' => $data['father_full_name'] ?? null,
            'father_phone_number' => $data['father_phone_number'] ?? null,
            'father_address' => $data['father_address'] ?? null,
            'mother_full_name' => $data['mother_full_name'] ?? null,
            'mother_phone_number' => $data['mother_phone_number'] ?? null,
            'mother_address' => $data['mother_address'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_relationship' => $data['emergency_contact_relationship'] ?? null,
            'emergency_contact_number' => $data['emergency_contact_number'] ?? null,
            'emergency_contact_address' => $data['emergency_contact_address'] ?? null,
        ]);

        // Assign student role
        $user->assignRole('student');

        // Make sure record is saved to academic year
        if (auth()->user()->school && auth()->user()->school->academicYear) {
            $currentAcademicYear = auth()->user()->school->academicYear;
            $studentRecord->academicYears()->sync([$currentAcademicYear->id => [
                'my_class_id' => $data['my_class_id'],
                'section_id' => $data['section_id'],
            ]]);
        }

        return $user;
    }

    /**
     * Create record for student.
     *
     * @param User         $student $name
     * @param array|object $record
     *
     * @throws InvalidValueException
     *
     * @return void
     */
    public function createStudentRecord(User $student, $record)
    {
        $record['admission_number'] = $record['admission_number'] ?? $this->generateAdmissionNumber();
        $section = $this->sectionService->getSectionById($record['section_id']);
        if (!$this->myClassService->getClassById($record['my_class_id'])->sections->contains($section)) {
            throw new InvalidValueException('Section is not in class');
        }

        if (auth()->user()->school->academic_year_id == null) {
            throw new EmptyRecordsException('Academic Year not set');
        }

        $student->studentRecord()->firstOrCreate([
            'user_id' => $student->id,
        ], [
            'my_class_id'      => $record['my_class_id'],
            'section_id'       => $record['section_id'],
            'admission_number' => $record['admission_number'],
            'admission_date'   => $record['admission_date'],
        ]);

        //create record history
        $currentAcademicYear = $student->school->academicYear;
        $student->studentRecord->load('academicYears')->academicYears()->sync([$currentAcademicYear->id => [
            'my_class_id' => $record['my_class_id'],
            'section_id'  => $record['section_id'],
        ]]);
    }

    /**
     * Update student.
     *
     * @param User $student
     * @param array $records
     * 
     * @return User
     */
    public function updateStudent(User $student, $records)
    {
        // Calculate age based on birthday if birthday was provided
        if (isset($records['birthday'])) {
            $records['age'] = \Carbon\Carbon::parse($records['birthday'])->age;
        }

        return $this->userService->updateUser($student, $records);
    }

    /**
     * Delete student.
     *
     * @param User $student
     * 
     * @return void
     */
    public function deleteStudent(User $student)
    {
        $student->delete();
    }

    /**
     * Generate admission number.
     * 
     * @param int|null $schoolId
     * 
     * @return string
     */
    public function generateAdmissionNumber($schoolId = null)
    {
        $schoolInitials = (School::find($schoolId) ?? auth()->user()->school)->initials;
        $schoolInitials = $schoolInitials != null ? $schoolInitials . '/' : '';
        $currentYear = date('y');
        do {
            $admissionNumber = "$schoolInitials" . "$currentYear/" . \mt_rand('100000', '999999');
            $uniqueAdmissionNumberFound = StudentRecord::where('admission_number', $admissionNumber)->count() <= 0;
        } while (!$uniqueAdmissionNumberFound);

        return $admissionNumber;
    }

    /**
     * Print student profile.
     *
     * @param string $name
     * @param string $view
     * @param array $data
     * 
     * @return \Illuminate\Http\Response
     */
    public function printProfile(string $name, string $view, array $data)
    {
        return PrintService::createPdfFromView($view, $data)->download($name . '.pdf');
    }

    /**
     * Promote students from one class to another.
     *
     * @param Collection $data Contains promotion details
     * @return Promotion
     * @throws InvalidValueException|EmptyRecordsException
     */
    public function promoteStudents(Collection $data)
    {
        // Debug incoming data
        \Log::info('Promotion data received:', $data->toArray());

        // Extract necessary data
        $fromClassId = $data->get('old_class_id');
        $toClassId = $data->get('new_class_id');
        $fromSectionId = $data->get('old_section_id');
        $toSectionId = $data->get('new_section_id');
        $students = $data->get('student_id', []);

        // Validate required fields
        if (!$fromClassId) {
            throw new InvalidValueException('From class is required');
        }

        if (!$toClassId) {
            throw new InvalidValueException('To class is required');
        }

        if (empty($students)) {
            throw new InvalidValueException('No students selected for promotion');
        }

        // Get the current academic year
        $academicYear = auth()->user()->school->academicYear;
        if (!$academicYear) {
            throw new EmptyRecordsException('Current Academic Year not set');
        }

        // Get the next academic year
        $nextAcademicYear = $data->get('next_academic_year_id');
        if (!$nextAcademicYear) {
            throw new InvalidValueException('Next Academic Year is required');
        }

        // Begin transaction to ensure data integrity
        DB::beginTransaction();
        try {
            // Create a new promotion record
            $promotion = Promotion::create([
                'school_id' => auth()->user()->school_id,
                'old_class_id' => $fromClassId,
                'new_class_id' => $toClassId,
                'old_section_id' => $fromSectionId,
                'new_section_id' => $toSectionId,
                'academic_year_id' => $academicYear->id,
                'next_academic_year_id' => $nextAcademicYear,
                'students' => $students,
            ]);

            // Log promotion creation
            \Log::info('Created promotion:', ['id' => $promotion->id, 'students_count' => count($students)]);

            // Process each student in the promotion
            $promotedCount = 0;
            foreach ($students as $studentId) {
                $student = User::find($studentId);
                if (!$student) {
                    \Log::warning('Student not found:', ['id' => $studentId]);
                    continue;
                }

                $studentRecord = $student->studentRecord;
                if (!$studentRecord) {
                    \Log::warning('Student record not found for student:', ['id' => $studentId]);
                    continue;
                }

                // Update the student's record for the new class and section
                $studentRecord->update([
                    'my_class_id' => $toClassId,
                    'section_id' => $toSectionId,
                ]);

                // Update or create academic year record for the student
                $studentRecord->academicYears()->syncWithoutDetaching([
                    $nextAcademicYear => [
                        'my_class_id' => $toClassId,
                        'section_id' => $toSectionId,
                    ],
                ]);

                $promotedCount++;
            }

            \Log::info('Successfully promoted students:', ['count' => $promotedCount]);

            DB::commit();
            return $promotion;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error promoting students: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        }
    }

    /**
     * Reset a specific promotion.
     *
     * @param Promotion $promotion
     * @return bool
     */
    public function resetPromotion(Promotion $promotion)
    {
        DB::beginTransaction();
        try {
            // Get all students from the promotion
            $students = $promotion->students;

            foreach ($students as $studentId) {
                $student = User::find($studentId);
                if (!$student || !$student->studentRecord) {
                    continue;
                }

                // Reset the student record to the original class and section
                $student->studentRecord->update([
                    'my_class_id' => $promotion->from_class_id,
                    'section_id' => $promotion->from_section_id,
                ]);

                // Remove the record from the next academic year
                if ($promotion->next_academic_year_id) {
                    $student->studentRecord->academicYears()->detach($promotion->next_academic_year_id);
                }
            }

            // Delete the promotion record
            $promotion->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get promotions for the current school
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPromotions()
    {
        return Promotion::where('school_id', auth()->user()->school_id)->get();
    }

    // Other methods remain unchanged
}
