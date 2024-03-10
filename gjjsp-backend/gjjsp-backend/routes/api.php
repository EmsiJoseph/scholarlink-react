<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScholarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScholarshipCategController;
use App\Http\Controllers\ProjectPartnerController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ScholarStatusController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\ScholarFamMemberController;
use App\Http\Controllers\HighschoolAcadDetailController;
use App\Http\Controllers\UndergradAcadDetailsController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TermGwaController;
use App\Http\Controllers\AlumniFormController;
use App\Http\Controllers\GraduatingFormController;
use App\Http\Controllers\RenewalDocumentController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ScholarExportController;
use App\Http\Controllers\GenerateReportController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Protected Route
Route::group(['middleware' => ['auth:sanctum']], function() {

    //CRUD USERS
    Route::apiResource('/users', UserController::class)->only(['index', 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/total-users', [UserController::class, 'totalUsers']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile/{id}', [UserController::class, 'updateProfile']);
    Route::get('/userScholar', [UserController::class, 'getUserRoles']);
    Route::get('/restore/{id}', [UserController::class, 'restore']);
    Route::get('/userScholars', [UserController::class, 'getScholars']);

    //Scholars Route
    Route::apiResource('/scholars', ScholarController::class)->only(['index', 'show']);
    Route::put('/scholars/{user_id}', [ScholarController::class, 'update']);
    Route::delete('/scholars/{user_id}', [ScholarController::class, 'destroy']);
    Route::get('/scholars/search/{user_id}', [ScholarController::class, 'search']);
    Route::get('/restore/{id}', [ScholarController::class, 'restoreScholar']);
    Route::put('/scholars-data/{user_id}', [ScholarController::class, 'updateOtherScholarProfile']);
    Route::post('school-data', [ScholarController::class, 'storeSchool']);
    Route::get('/total-scholars', [ScholarController::class, 'totalScholars']);

    //Scholars Profile Route
    Route::post('/scholarsProfile', [ScholarController::class, 'storeScholarProfile']);
    Route::put('/scholarsProfile/{id}', [ScholarController::class, 'updateScholarProfile']);
    Route::get('/scholarsProfile', [ScholarController::class, 'scholarProfile']);

    //Roles Route
    Route::apiResource('/roles', RoleController::class)->only(['index', 'show']);

    //Auth Route
    Route::post('/logout', [AuthController::class, 'logout']);

   // Route::get('/refresh-token', [AuthController::class,'refreshToken']);

   //Scholarship Route
    Route::apiResource('/scholarships', ScholarshipCategController::class)->only(['index', 'show']);
    Route::get('/total-scholarships', [ScholarshipCategController::class, 'totalScholarships']);
    Route::post('/scholarships', [ScholarshipCategController::class, 'store']);
    Route::put('/scholarships/{id}', [ScholarshipCategController::class, 'update']);
    Route::delete('/scholarships/{id}', [ScholarshipCategController::class, 'destroy']);
    Route::get('/restoreScholarships/{id}', [ScholarshipCategController::class, 'restoreScholarship']);

    //ProjectPartner Route
    Route::apiResource('/project-partners', ProjectPartnerController::class)->only(['index', 'show']);
    Route::get('/total-project-partners', [ProjectPartnerController::class, 'totalProjectPartners']);
    Route::post('/project-partners', [ProjectPartnerController::class, 'store']);
    Route::put('/project-partners/{id}', [ProjectPartnerController::class, 'update']);
    Route::delete('/project-partners/{id}', [ProjectPartnerController::class, 'destroy']);
    Route::get('/restoreProjectPartners/{id}', [ProjectPartnerController::class, 'restoreProjectPartner']);
    Route::apiResource('/scholar-status', ScholarStatusController::class)->only(['index', 'show']);

    //Scholar Fam Member Route
    Route::put('/scholarFam/{id}', [ScholarFamMemberController::class, 'update']);
    Route::get('/scholarFam', [ScholarFamMemberController::class, 'getScholarFam']);

    //High School Acad Detail Route
    Route::get('/highschool-acad-detail', [HighschoolAcadDetailController::class, 'getHighschoolAcadDetail']);
    Route::put('/highschool-acad-detail/{id}', [HighschoolAcadDetailController::class, 'update']);
    Route::delete('/highschool-acad-detail/{id}', [HighschoolAcadDetailController::class, 'destroy']);
    Route::get('/restore-highschool/{id}', [HighschoolAcadDetailController::class, 'restoreHighschoolAcadDetail']);

    //Undergrad Acad Detail Route
    Route::put('/undergrad-acad-detail/{id}', [UndergradAcadDetailsController::class, 'update']);
    Route::delete('/undergrad-acad-detail/{id}', [UndergradAcadDetailsController::class, 'destroy']);
    Route::get('/restore-undergrad/{id}', [UndergradAcadDetailsController::class, 'restoreUndergradAcadDetail']);
    Route::get('/undergrad-acad-detail', [UndergradAcadDetailsController::class, 'getUndergradAcadDetail']);

    //Submission Route
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submission');
    Route::post('/submissions', [SubmissionController::class, 'store']);

    //Alumni Form Route
    Route::get('/alumni-form', [AlumniFormController::class, 'index'])->name('alumni-form');
    Route::post('/alumni-form', [AlumniFormController::class, 'store']);

    //Graduating Form Route
    Route::get('/graduating-form', [GraduatingFormController::class, 'index'])->name('graduating-form');
    Route::post('/graduating-form', [GraduatingFormController::class, 'store']);

    //Renewal Form Route
    Route::get('/term-gwa', [TermGwaController::class, 'index'])->name('term-gwa');
    Route::post('/term-gwa', [TermGwaController::class, 'store']);

    //Document Route
    Route::get('/documents', [DocumentController::class, 'index'])->name('document');
    Route::post('/documents', [DocumentController::class, 'store']);

    Route::get('/renewal-documents', [RenewalDocumentController::class, 'index', 'show']);
    Route::post('/renewal-documents', [RenewalDocumentController::class, 'store']);
    Route::put('/renewal-documents/{id}', [RenewalDocumentController::class, 'update']);
    Route::get('renewal-document' , [RenewalDocumentController::class, 'scholarSubmission']);
    Route::get('total-renewal' , [RenewalDocumentController::class, 'totalRenewalDocuments']);
    Route::get('/scholar-renewal-documents', [RenewalDocumentController::class, 'scholarRenewalDocuments']);

    Route::get('/graduating-documents', [GraduatingFormController::class, 'index', 'show']);
    Route::post('/graduating-documents', [GraduatingFormController::class, 'store']);

    //School Route
    Route::apiResource('/schools', SchoolController::class)->only(['index', 'show']);
    Route::post('/schools', [SchoolController::class, 'store']);
    Route::put('/schools/{id}', [SchoolController::class, 'update']);
    Route::delete('/schools/{id}', [SchoolController::class, 'destroy']);
    Route::get('/restore-schools/{id}', [SchoolController::class, 'restoreSchool']);



});

  //Export Route
  Route::get('/export-scholars', [ScholarExportController::class, 'exportScholars'])->name('export.scholars');

  //Repor Generation Route
  Route::get('/generate-report',[GenerateReportController::class, 'generateReport'])->name( 'generate.report' );



//Public Route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/generate-prompt', [PromptController::class, 'generate']);

Route::middleware('auth:api')->get('/scholar', function (Request $request){
    return $request->scholar();;
});

Route::middleware('auth:api')->get('/user', function (Request $request){
    return $request->scholar();;
});

