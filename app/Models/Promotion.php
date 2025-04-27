<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_class_id',
        'new_class_id',
        'old_section_id',
        'new_section_id',
        'academic_year_id',
        'students',
        'school_id',
    ];

    protected $casts = [
        'students' => 'array',
    ];

    public function getLabelAttribute()
    {
        return "{$this->oldClass->name} - {$this->oldSection->name} to {$this->newClass->name} - {$this->newSection->name} year: {$this->academicYear->start_year} - {$this->academicYear->stop_year}";
    }

    public function oldClass(): BelongsTo
    {
        return $this->belongsTo(MyClass::class, 'old_class_id');
    }

    public function newClass(): BelongsTo
    {
        return $this->belongsTo(MyClass::class, 'new_class_id');
    }

    public function oldSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'old_section_id');
    }

    public function newSection(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'new_section_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function promoteStudents()
    {
        if (empty($this->students)) {
            return false;
        }

        $academicYear = AcademicYear::find($this->academic_year_id);
        $newAcademicYear = $academicYear; // Default to same year unless specified differently

        foreach ($this->students as $studentId) {
            $studentRecord = StudentRecord::find($studentId);
            
            if (!$studentRecord) {
                continue;
            }
            
            // Update student's class and section for the current record
            $studentRecord->my_class_id = $this->new_class_id;
            $studentRecord->section_id = $this->new_section_id;
            $studentRecord->save();
            
            // Create or update academic year record for this student
            $academicYearRecord = AcademicYearStudentRecord::updateOrCreate([
                'academic_year_id' => $newAcademicYear->id,
                'student_record_id' => $studentRecord->id,
            ], [
                'my_class_id' => $this->new_class_id,
                'section_id' => $this->new_section_id,
            ]);
        }
        
        return true;
    }
}
