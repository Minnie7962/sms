<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'section_id',
        'subject_id',
        'exam_slot_id',
        'student_marks',
    ];

    /**
     * Get the subject that owns the ExamRecord.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the examSlot that owns the ExamRecord.
     */
    public function examSlot(): BelongsTo
    {
        return $this->belongsTo(ExamSlot::class);
    }

    public function scopeinSubject($query, $subject_id)
    {
        return $query->where('subject_id', $subject_id);
    }

    public function scopeinSection($query, $section_id)
    {
        return $query->where('section_id', $section_id);
    }

    // In ExamRecord.php
    public static function addOrUpdateBatch($examId, $sectionId, $students, $marks)
    {
        $exam = Exam::findOrFail($examId);
        $examSlot = $exam->examSlots()->first(); // Assuming single exam slot for simplified grading

        if (!$examSlot) {
            // Create a default exam slot if none exists
            $examSlot = ExamSlot::create([
                'name' => 'Default',
                'description' => 'Default exam slot',
                'total_marks' => 100,
                'exam_id' => $exam->id
            ]);
        }

        $records = [];

        foreach ($students as $index => $studentId) {
            $record = self::updateOrCreate(
                [
                    'user_id' => $studentId,
                    'section_id' => $sectionId,
                    'exam_slot_id' => $examSlot->id,
                ],
                [
                    'student_marks' => $marks[$index] ?? 0,
                ]
            );

            $records[] = $record;
        }

        return $records;
    }
}
