<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyClass extends Model
{
    use HasFactory;
    use SoftDeletes;

    const KINDERGARTEN = 'Kindergarten';
    const GRADE_1 = 'Grade 1';
    const GRADE_2 = 'Grade 2';
    const GRADE_3 = 'Grade 3';
    const GRADE_4 = 'Grade 4';
    const GRADE_5 = 'Grade 5';
    const GRADE_6 = 'Grade 6';

    protected $fillable = ['name', 'class_group_id'];

    public function school()
    {
        $this->hasOneThrough(School::class, ClassGroup::class);
    }

    /**
     * Get the classGroup that owns the MyClass.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }

    /**
     * Get all of the sections for the MyClass.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get all of the students for the MyClass.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentRecords()
    {
        return $this->hasMany(StudentRecord::class);
    }

    /**
     * The subjects that belong to the MyClass.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get the students in class.
     */
    public function students(): Collection
    {
        $students = User::students()->inSchool()->whereRelation('studentRecord.myClass', 'id', $this->id)->get();

        return $students;
    }

    /**
     * Get all of the syllabi for the MyClass.
     */
    public function syllabi(): HasManyThrough
    {
        return $this->hasManyThrough(Syllabus::class, Subject::class);
    }

    /**
     * Get all of the timetables for the MyClass.
     */
    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public static function getGradeLevels()
    {
        return [
            self::KINDERGARTEN,
            self::GRADE_1,
            self::GRADE_2,
            self::GRADE_3,
            self::GRADE_4,
            self::GRADE_5,
            self::GRADE_6,
        ];
    }

    // Get students count for the current academic year
    public function getCurrentStudentsCount()
    {
        $currentAcademicYear = AcademicYear::where('school_id', $this->classGroup->school_id)
            ->where('is_current', true)
            ->first();

        if (!$currentAcademicYear) {
            return 0;
        }

        return AcademicYearStudentRecord::where('academic_year_id', $currentAcademicYear->id)
            ->where('my_class_id', $this->id)
            ->count();
    }
}
