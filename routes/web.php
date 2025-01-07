<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ExamRuleController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\GradeRuleController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\GradingSystemController;
use App\Http\Controllers\SchoolSessionController;
use App\Http\Controllers\AcademicSettingController;
use App\Http\Controllers\AssignedTeacherController;
use App\Http\Controllers\Auth\UpdatePasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['reset' => false]);

Route::middleware(['auth'])->group(function () {

    Route::prefix('school')->name('school.')->group(function () {
        // School-related routes
        Route::post('session/create', [SchoolSessionController::class, 'store'])->name('session.store');
        Route::post('session/browse', [SchoolSessionController::class, 'browse'])->name('session.browse');

        Route::post('semester/create', [SemesterController::class, 'store'])->name('semester.create');
        Route::post('final-marks-submission-status/update', [AcademicSettingController::class, 'updateFinalMarksSubmissionStatus'])->name('final.marks.submission.status.update');

        Route::post('attendance/type/update', [AcademicSettingController::class, 'updateAttendanceType'])->name('attendance.type.update');

        // Class
        Route::post('class/create', [SchoolClassController::class, 'store'])->name('class.create');
        Route::post('class/update', [SchoolClassController::class, 'update'])->name('class.update');

        // Sections
        Route::post('section/create', [SectionController::class, 'store'])->name('section.create');
        Route::post('section/update', [SectionController::class, 'update'])->name('section.update');

        // Courses
        Route::post('course/create', [CourseController::class, 'store'])->name('course.create');
        Route::post('course/update', [CourseController::class, 'update'])->name('course.update');

        // Teacher
        Route::post('teacher/create', [UserController::class, 'storeTeacher'])->name('teacher.create');
        Route::post('teacher/update', [UserController::class, 'updateTeacher'])->name('teacher.update');
        Route::post('teacher/assign', [AssignedTeacherController::class, 'store'])->name('teacher.assign');

        // Student
        Route::post('student/create', [UserController::class, 'storeStudent'])->name('student.create');
        Route::post('student/update', [UserController::class, 'updateStudent'])->name('student.update');
    });

    Route::get('/home', [HomeController::class, 'index'])->name('dashboard');

    // Attendance routes
    Route::group(['prefix' => 'attendances'], function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/view', [AttendanceController::class, 'show'])->name('attendance.list.show');
        Route::get('/take', [AttendanceController::class, 'create'])->name('attendance.create.show');
        Route::post('/', [AttendanceController::class, 'store'])->name('attendances.store');
    });

    // Classes and sections routes
    Route::group(['prefix' => 'classes'], function () {
        Route::get('/', [SchoolClassController::class, 'index']);
        Route::get('/edit/{id}', [SchoolClassController::class, 'edit'])->name('class.edit');
    });
    Route::group(['prefix' => 'sections'], function () {
        Route::get('/', [SectionController::class, 'getByClassId'])->name('get.sections.courses.by.classId');
        Route::get('/edit/{id}', [SectionController::class, 'edit'])->name('section.edit');
    });

    // Teachers routes
    Route::group(['prefix' => 'teachers'], function () {
        Route::get('/add', function () {
            return view('teachers.add');
        })->name('teacher.create.show');
        Route::get('/edit/{id}', [UserController::class, 'editTeacher'])->name('teacher.edit.show');
        Route::get('/list', [UserController::class, 'getTeacherList'])->name('teacher.list.show');
        Route::get('/profile/{id}', [UserController::class, 'showTeacherProfile'])->name('teacher.profile.show');
    });

    // Students routes
    Route::group(['prefix' => 'students'], function () {
        Route::get('/add', [UserController::class, 'createStudent'])->name('student.create.show');
        Route::get('/edit/{id}', [UserController::class, 'editStudent'])->name('student.edit.show');
        Route::get('/list', [UserController::class, 'getStudentList'])->name('student.list.show');
        Route::get('/profile/{id}', [UserController::class, 'showStudentProfile'])->name('student.profile.show');
        Route::get('/attendance/{id}', [AttendanceController::class, 'showStudentAttendance'])->name('student.attendance.show');
    });

    // Marks routes
    Route::group(['prefix' => 'marks'], function () {
        Route::get('/create', [MarkController::class, 'create'])->name('course.mark.create');
        Route::post('/store', [MarkController::class, 'store'])->name('course.mark.store');
        Route::get('/results', [MarkController::class, 'index'])->name('course.mark.list.show');
        Route::get('/view', [MarkController::class, 'showCourseMark'])->name('course.mark.show');
        Route::get('/final/submit', [MarkController::class, 'showFinalMark'])->name('course.final.mark.submit.show');
        Route::post('/final/submit', [MarkController::class, 'storeFinalMark'])->name('course.final.mark.submit.store');
    });

    // Exams routes
    Route::group(['prefix' => 'exams'], function () {
        Route::get('/view', [ExamController::class, 'index'])->name('exam.list.show');
        Route::post('/create', [ExamController::class,'store'])->name('exam.create');
        Route::get('/create', [ExamController::class, 'create'])->name('exam.create.show');
        Route::get('/add-rule', [ExamRuleController::class, 'create'])->name('exam.rule.create');
        Route::post('/add-rule', [ExamRuleController::class, 'store'])->name('exam.rule.store');
        Route::get('/edit-rule', [ExamRuleController::class, 'edit'])->name('exam.rule.edit');
        Route::post('/edit-rule', [ExamRuleController::class, 'update'])->name('exam.rule.update');
        Route::get('/view-rule', [ExamRuleController::class, 'index'])->name('exam.rule.show');
        Route::get('/grade/create', [GradingSystemController::class, 'create'])->name('exam.grade.system.create');
        Route::post('/grade/create', [GradingSystemController::class, 'store'])->name('exam.grade.system.store');
        Route::get('/grade/view', [GradingSystemController::class, 'index'])->name('exam.grade.system.index');
        Route::get('/grade/add-rule', [GradeRuleController::class, 'create'])->name('exam.grade.system.rule.create');
        Route::post('/grade/add-rule', [GradeRuleController::class, 'store'])->name('exam.grade.system.rule.store');
        Route::get('/grade/view-rules', [GradeRuleController::class, 'index'])->name('exam.grade.system.rule.show');
        Route::post('/grade/delete-rule', [GradeRuleController::class, 'destroy'])->name('exam.grade.system.rule.delete');
    });

    // Promotions routes
    Route::group(['prefix' => 'promotions'], function () {
        Route::get('/index', [PromotionController::class, 'index'])->name('promotions.index');
        Route::get('/promote', [PromotionController::class, 'create'])->name('promotions.create');
        Route::post('/promote', [PromotionController::class, 'store'])->name('promotions.store');
    });

    // Academic settings route
    Route::get('/academics/settings', [AcademicSettingController::class, 'index']);

    // Calendar events route
    Route::group(['prefix' => 'calendar'], function () {
        Route::get('event', [EventController::class, 'index'])->name('events.show');
        Route::post('crud-ajax', [EventController::class, 'calendarEvents'])->name('events.crud');
    });

    // Routines routes
    Route::group(['prefix' => 'routine'], function () {
        Route::get('/create', [RoutineController::class, 'create'])->name('section.routine.create');
        Route::get('/view', [RoutineController::class, 'show'])->name('section.routine.show');
        Route::post('/store', [RoutineController::class, 'store'])->name('section.routine.store');
    });

    // Syllabus routes
    Route::group(['prefix' => 'syllabus'], function () {
        Route::get('/create', [SyllabusController::class, 'create'])->name('class.syllabus.create');
        Route::post('/create', [SyllabusController::class, 'store'])->name('syllabus.store');
        Route::get('/index', [SyllabusController::class, 'index'])->name('course.syllabus.index');
    });

    // Notices routes
    Route::group(['prefix' => 'notice'], function () {
        Route::get('/create', [NoticeController::class, 'create'])->name('notice.create');
        Route::post('/create', [NoticeController::class, 'store'])->name('notice.store');
    });

    // Courses routes
    Route::group(['prefix' => 'courses'], function () {
        Route::get('teacher/index', [AssignedTeacherController::class, 'getTeacherCourses'])->name('course.teacher.list.show');
        Route::get('student/index/{student_id}', [CourseController::class, 'getStudentCourses'])->name('course.student.list.show');
        Route::get('edit/{id}', [CourseController::class, 'edit'])->name('course.edit');
    });

    // Assignment routes
    Route::group(['prefix' => 'assignments'], function () {
        Route::get('/index', [AssignmentController::class, 'getCourseAssignments'])->name('assignment.list.show');
        Route::get('/create', [AssignmentController::class, 'create'])->name('assignment.create');
        Route::post('/create', [AssignmentController::class, 'store'])->name('assignment.store');
    });

    // Update password route
    Route::get('password/edit', [UpdatePasswordController::class, 'edit'])->name('password.edit');
    Route::post('password/update', [UpdatePasswordController::class, 'update'])->name('password.update.custom');
});
