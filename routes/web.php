<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\CheckRegistrationStatus;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\EditUserProfileController;
use App\Http\Controllers\ManagerDashboardController;
use App\Http\Controllers\AdminControllerSatu;
use App\Http\Controllers\PublicControllerSatu;


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

Route::redirect('/', '/register');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

Route::get('/announcement', [HomeController::class, 'index'])->name('index');

// temporary public
Route::get('/wari/private/results', [PublicController::class, 'userResults'])->name('public.user.results');
Route::get('/wari/private/results/{user}', [PublicController::class, 'userResultsDetail'])->name('public.user.results.detail');

Route::get('/export/excel_calon_kandidat', [HomeController::class, 'exportExcelCalonKandidat'])->name('export.calon.kandidat');
Route::get('/export/excel', [HomeController::class, 'exportExcel'])->name('export.excel');
Route::get('/export/hasil_psikotes.pdf', [HomeController::class, 'exportPDF'])->name('export.pdf');

Route::middleware([CheckRegistrationStatus::class])->group(function () {
    Auth::routes(['register' => true]);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/admin/registration-toggle', [AdminController::class, 'toggleRegistration'])->name('admin.toggle.registration');
});

/*------------------------------------------
--------------------------------------------
All Normal Users Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:user'])->group(function () {

    Route::get('/home', [UserController::class, 'index'])->name('home')->middleware(['auth', 'checkProfileSubmission']);

    Route::get('/input-profile-data', [UserController::class, 'inputProfileData'])->name('input.profile.data');
    Route::post('/input-profile-data/store', [UserController::class, 'storeProfileData'])->name('store.profile.data');

    Route::get('/profile', [EditUserProfileController::class, 'profile'])->name('profile');
    Route::delete('/profile', [EditUserProfileController::class, 'deleteCV'])->name('profile.deleteCV');

    Route::get('/profile/edit', [EditUserProfileController::class, 'profileEdit'])->name('profile.edit');
    Route::post('/profile/edit', [EditUserProfileController::class, 'profileUpdate'])->name('profile.update');

    Route::get('/change-password', [UserController::class, 'changePassword'])->name('changePassword');
    Route::post('/change-password', [UserController::class, 'updatePassword'])->name('updatePassword');

    Route::get('/exam', [UserController::class, 'exam'])->name('exam');
    Route::get('/instruction/{quiz}', [UserController::class, 'instruction'])->name('instruction');
    Route::get('/exam/{quiz}', [UserController::class, 'detailQuiz'])->name('detail.quiz');
    Route::post('/exam/{quiz}/answers', [UserController::class, 'answersStore'])->name('answers.store');

    Route::get('/results', [UserController::class, 'showResults'])->name('results');

    Route::get('auth/{provice}', [RegisterController::class, 'redirectToProvice']);
    Route::get('auth/{provice}/callback', [RegisterController::class, 'handleProviderCallback']);
});

/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:admin'])->group(function () {

    Route::get('/admin/home', [AdminController::class, 'adminHome'])->name('admin.home');

    Route::get('/admin/control-panel', [AdminController::class, 'adminControl'])->name('admin.control');
    Route::post('/admin/control-panel', [AdminController::class, 'updateAnnouncement'])->name('admin.updateAnnouncement');
    Route::post('/admin/reset-answers', [AdminController::class, 'resetAnswers'])->name('answers.reset');

    Route::get('/admin/upload', [AdminController::class, 'adminUpload'])->name('admin.upload');
    Route::post('/admin/upload/upload', [AdminController::class, 'adminUploadImage'])->name('admin.uploadimage');
    Route::post('/admin/upload/delete', [AdminController::class, 'imageDelete'])->name('image.delete');

    Route::get('/admin/manage-score', [AdminController::class, 'manageScore'])->name('admin.manageScore');
    Route::post('/admin/update-avg-score', [AdminController::class, 'updateAvgScore'])->name('admin.updateAvgScore');
    Route::post('/admin/update-baik-sekali', [AdminController::class, 'updateBaikSekali'])->name('admin.updateBaikSekali');
    Route::post('/admin/update-baik', [AdminController::class, 'updateBaik'])->name('admin.updateBaik');
    Route::post('/admin/update-cukup', [AdminController::class, 'updateCukup'])->name('admin.updateCukup');

    Route::get('/admin/branch', [AdminDashboardController::class, 'adminBranch'])->name('admin.branch');
    Route::get('/admin/branch/manage-branch', [AdminDashboardController::class, 'manageBranch'])->name('manage.branch');
    Route::post('/admin/branch/manage-branch/edit', [AdminDashboardController::class, 'editBranch'])->name('edit.branch');
    Route::post('/admin/branch/manage-branch/delete', [AdminDashboardController::class, 'deleteBranch'])->name('delete.branch');
    Route::post('/admin/branch/manage-branch/store', [AdminDashboardController::class, 'storeBranch'])->name('store.branch');

    Route::get('/admin/branch/jakarta', [AdminDashboardController::class, 'adminJKTBranch'])->name('admin.jktbranch');
    Route::get('/admin/branch/tangerang', [AdminDashboardController::class, 'adminTGRBranch'])->name('admin.tgrbranch');
    Route::get('/admin/branch/others', [AdminDashboardController::class, 'adminOthersBranch'])->name('admin.othersbranch');
    Route::get('/admin/branch/detail/{branch_location}', [AdminDashboardController::class, 'adminBranchDetail'])->name('admin.branchDetail');

    Route::get('/admin/profile', [AdminController::class, 'adminProfile'])->name('admin.profile');

    Route::get('/admin/change-password', [AdminController::class, 'adminChangePassword'])->name('admin.changePassword');
    Route::post('/admin/change-password', [AdminController::class, 'adminUpdatePassword'])->name('admin.updatePassword');

    Route::get('/admin/users', [AdminController::class, 'adminUsers'])->name('admin.users');
    Route::get('/admin/users/{user}', [AdminController::class, 'showUsers'])->name('users.show');
    Route::post('/admin/users/{user}/follow_up', [AdminController::class, 'followUp'])->name('users.follow_up');
    Route::post('/admin/users/{user}/unfollow', [AdminController::class, 'unfollow'])->name('users.unfollow');
    Route::delete('/admin/users/{user}/reset-quiz', [AdminController::class, 'resetQuiz'])->name('users.resetQuiz');
    Route::delete('/admin/users/delete-user', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
    Route::delete('/admin/users/deleteAllUsers', [AdminController::class, 'deleteAllUsers'])->name('admin.deleteAllUsers');

    Route::get('/admin/manager', [AdminController::class, 'adminManager'])->name('admin.manager');
    Route::get('/admin/manager/{user}', [AdminController::class, 'showManager'])->name('manager.show');
    Route::post('/admin/manager/add-manager', [AdminController::class, 'addManager'])->name('admin.addManager');
    Route::post('/admin/manager/update', [AdminController::class, 'updateManager'])->name('admin.updateManager');
    Route::post('/admin/manager/reset-password', [AdminController::class, 'resetManagerPassword'])->name('admin.resetManagerPassword');
    Route::delete('/admin/manager/delete-manager', [AdminController::class, 'deleteManager'])->name('admin.deleteManager');
    
    Route::get('/admin/manage-exam', [QuizController::class, 'manageExam'])->name('manage.exam');
    Route::post('/admin/manage-exam/store', [QuizController::class, 'storeExam'])->name('store.exam');
    Route::post('/admin/manage-exam/edit', [QuizController::class, 'manageExamEdit'])->name('manage.exam.edit');
    Route::delete('/admin/manage-exam/delete', [QuizController::class, 'manageExamDelete'])->name('manage.exam.delete');

    Route::get('/admin/manage-exam-question', [QuizController::class, 'manageExamQuestion'])->name('manage.examQuestion');
    Route::post('/admin/manage-exam-question', [QuizController::class, 'manageExamQuestionUpdate'])->name('manage.examQuestion.Update');

    Route::get('/admin/manage-exam-question/multiple-choice/{quiz}', [QuizController::class, 'detailExam'])->name('detail.exam');
    Route::post('/admin/manage-exam-question/multiple-choice/{quiz}/store', [QuizController::class, 'storeQuestion'])->name('store.question');
    Route::post('/admin/manage-exam-question/multiple-choice/{quiz}/store-image', [QuizController::class, 'storeQuestionImage'])->name('store.question.image');
    Route::post('/admin/manage-exam-question/multiple-choice/{quiz}/edit', [QuizController::class, 'questionEdit'])->name('question.edit');
    Route::delete('/admin/manage-exam-question/multiple-choice/{quiz}/delete', [QuizController::class, 'questionDelete'])->name('question.delete');
    Route::post('/admin/manage-exam-question/multiple-choice/{quiz}/bulk-insert', [QuizController::class, 'questionBulkInsert'])->name('question.bulk-insert');
    Route::post('/admin/manage-exam-question/delete-questions/{quiz}', [QuizController::class, 'deleteQuestionsForQuiz'])->name('delete.questions.for.quiz');

    Route::get('/admin/manage-exam-question/essay/{quiz}', [QuizController::class, 'essayExam'])->name('essay.exam');
    Route::post('/admin/manage-exam-question/essay/{quiz}/store', [QuizController::class, 'storeEssay'])->name('store.essay');
    // Route::post('/admin/manage-exam-question/essay/{quiz}/store-image', [QuizController::class, 'storeEssayImage'])->name('store.essay.image');
    Route::post('/admin/manage-exam-question/essay/{quiz}/edit', [QuizController::class, 'essayEdit'])->name('essay.edit');
    Route::post('/admin/manage-exam-question/essay/{quiz}/bulk-insert', [QuizController::class, 'essayBulkInsert'])->name('essay.bulk-insert');

    Route::get('/admin/manage-exam-question/example/multiple-choice/{quiz}', [QuizController::class, 'exampleExam'])->name('example.exam');
    Route::get('/admin/manage-exam-question/example/essay/{quiz}', [QuizController::class, 'essayExample'])->name('essay.example');

    Route::get('/admin/manage-exam-question/preview/{quiz}', [AdminController::class, 'previewQuiz'])->name('preview.quiz');
    Route::get('/admin/manage-exam-question/instruction/{quiz}', [AdminController::class, 'instructionQuiz'])->name('instruction.quiz');

    Route::get('/admin/results', [AdminController::class, 'userResults'])->name('user.results');
    Route::get('/admin/results/{user}', [AdminController::class, 'userResultsDetail'])->name('user.results.detail');

    Route::get('/admin/followed-user', [AdminController::class, 'followedUser'])->name('followed.user');
    Route::get('/admin/user-you-follow', [AdminController::class, 'userFollowingCount'])->name('user.followingCount');
    Route::get('/admin/candidate', [AdminController::class, 'candidate'])->name('user.candidate');

    Route::get('/admin/job-position', [AdminController::class, 'jobPosition'])->name('job.position');
    Route::post('/admin/job-position/edit', [AdminController::class, 'editJobPosition'])->name('edit.jobPosition');
    Route::delete('/admin/job-position/delete', [AdminController::class, 'deleteJobPosition'])->name('delete.jobPosition');
    Route::post('/admin/job-position/store', [AdminController::class, 'storeJobPosition'])->name('store.jobPosition');

    Route::get('/admin/download-multiple_choice-xlsx', [AdminController::class, 'downloadMultipleChoiceXlsx'])->name('download.multiple.choice.xlsx');
    Route::get('/admin/download-essay-xlsx', [AdminController::class, 'downloadEssayXlsx'])->name('download.essay.xlsx');
});

/*------------------------------------------
--------------------------------------------
All Manager Routes List
--------------------------------------------
--------------------------------------------*/
Route::middleware(['auth', 'user-access:manager'])->group(function () {

    Route::get('/manager/home', [ManagerController::class, 'managerHome'])->name('manager.home');
    Route::get('/manager/branch', [ManagerDashboardController::class, 'managerBranch'])->name('manager.branch');
    Route::get('/manager/branch/jakarta', [ManagerDashboardController::class, 'managerJKTBranch'])->name('manager.jktbranch');
    Route::get('/manager/branch/tangerang', [ManagerDashboardController::class, 'managerTGRBranch'])->name('manager.tgrbranch');
    Route::get('/manager/branch/others', [ManagerDashboardController::class, 'managerOthersBranch'])->name('manager.othersbranch');
    Route::get('/manager/branch/detail/{branch_location}', [ManagerDashboardController::class, 'managerBranchDetail'])->name('manager.branchDetail');

    Route::get('/manager/profile', [ManagerController::class, 'managerProfile'])->name('manager.profile');

    Route::get('/manager/change-password', [ManagerController::class, 'managerChangePassword'])->name('manager.changePassword');
    Route::post('/manager/change-password', [ManagerController::class, 'managerUpdatePassword'])->name('manager.updatePassword');

    Route::get('/manager/users', [ManagerController::class, 'managerUsers'])->name('manager.users');
    Route::get('/manager/users/{user}', [ManagerController::class, 'managerShowUsers'])->name('managerusers.show');
    Route::post('/manager/users/{user}/follow_up', [ManagerController::class, 'followUp'])->name('manager.follow_up');
    Route::post('/manager/users/{user}/unfollow', [ManagerController::class, 'unfollow'])->name('manager.unfollow');

    Route::get('/manager/exam-question', [QuizController::class, 'managerExamQuestion'])->name('manager.examQuestion');
    Route::get('/manager/exam-question/preview/{quiz}', [ManagerController::class, 'managerPreviewQuiz'])->name('manager.preview.quiz');
    Route::get('/manager/exam-question/instruction/{quiz}', [ManagerController::class, 'MinstructionQuiz'])->name('Minstruction.quiz');

    Route::get('/manager/results', [ManagerController::class, 'MuserResults'])->name('Muser.results');
    Route::get('/manager/results/{user}', [ManagerController::class, 'MuserResultsDetail'])->name('Muser.results.detail');

    Route::get('/manager/followed-user', [ManagerController::class, 'mFollowedUser'])->name('mfollowed.user');
    Route::get('/manager/user-you-follow', [ManagerController::class, 'mUserFollowingCount'])->name('muser.followingCount');

    Route::get('/manager/candidate', [ManagerController::class, 'mCandidate'])->name('muser.candidate');
});
    
/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No Login Required)
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicControllerSatu::class, 'home'])->name('home');
Route::get('/motor/{id}', [PublicControllerSatu::class, 'motorDetail'])->name('motor.detail');
Route::get('/motors/category/{slug}', [PublicControllerSatu::class, 'motorsByCategory'])->name('motors.category');
Route::get('/compare', [PublicControllerSatu::class, 'compare'])->name('motors.compare');
Route::get('/accessories', [PublicControllerSatu::class, 'accessories'])->name('accessories.index');
Route::get('/apparels', [PublicControllerSatu::class, 'apparels'])->name('apparels.index');
Route::get('/branches', [PublicControllerSatu::class, 'branches'])->name('branches.index');
Route::get('/price-list', [PublicControllerSatu::class, 'priceList'])->name('price.list');

Route::get('/test-ride', [PublicControllerSatu::class, 'showTestRideForm'])->name('test-ride.form');
Route::post('/test-ride', [PublicControllerSatu::class, 'submitTestRide'])->name('test-ride.submit');

Route::get('/credit-simulation', [PublicControllerSatu::class, 'showCreditForm'])->name('credit.form');
Route::post('/credit-simulation', [PublicControllerSatu::class, 'submitCreditSimulation'])->name('credit.submit');

/*
|--------------------------------------------------------------------------
| ADMIN LOGIN (Public - No Auth Middleware)
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Login Required, Must be Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'user-access:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/home', [AdminController::class, 'adminHome'])->name('home');

    // Motors
    Route::get('/motor', [AdminControllerSatu::class, 'adminMotor'])->name('motor');
    Route::get('/motor/data', [AdminControllerSatu::class, 'getMotorData'])->name('motor.data');
    Route::get('/motors', [AdminControllerSatu::class, 'motorsIndex'])->name('motors.index');
    Route::get('/motors/create', [AdminControllerSatu::class, 'motorsCreate'])->name('motors.create');
    Route::post('/motors/store', [AdminControllerSatu::class, 'motorsStore'])->name('motors.store');
    Route::get('/motors/edit/{id}', [AdminControllerSatu::class, 'motorsEdit'])->name('motors.edit');
    Route::post('/motors/update', [AdminControllerSatu::class, 'updateMotor'])->name('motors.update');
    Route::post('/motors/delete', [AdminControllerSatu::class, 'deleteMotor'])->name('motors.delete');
    Route::get('/motor/colors', [AdminControllerSatu::class, 'colorsIndex'])->name('motor-color.index');

    // Features
    Route::get('/features', [AdminControllerSatu::class, 'featuresIndex'])->name('features.index');
    Route::post('/features', [AdminControllerSatu::class, 'featuresStore'])->name('features.store');
    Route::get('/features/{id}/edit', [AdminControllerSatu::class, 'featuresEdit'])->name('features.edit');
    Route::put('/features/{id}', [AdminControllerSatu::class, 'featuresUpdate'])->name('features.update');
    Route::delete('/features/{id}', [AdminControllerSatu::class, 'featuresDelete'])->name('features.delete');

    // Colors
    Route::post('/colors', [AdminControllerSatu::class, 'colorsStore'])->name('colors.store');
    Route::get('/colors/{id}/edit', [AdminControllerSatu::class, 'colorsEdit'])->name('colors.edit');
    Route::put('/colors/{id}', [AdminControllerSatu::class, 'colorsUpdate'])->name('colors.update');
    Route::delete('/colors/{id}', [AdminControllerSatu::class, 'colorsDelete'])->name('colors.delete');

    // Specifications
    Route::get('/specs', [AdminControllerSatu::class, 'specsIndex'])->name('specs.index');
    Route::post('/specs', [AdminControllerSatu::class, 'specsStore'])->name('specs.store');
    Route::get('/specs/{id}/edit', [AdminControllerSatu::class, 'specsEdit'])->name('specs.edit');
    Route::put('/specs/{id}', [AdminControllerSatu::class, 'specsUpdate'])->name('specs.update');
    Route::delete('/specs/{id}', [AdminControllerSatu::class, 'specsDelete'])->name('specs.delete');

    // Accessories
    Route::post('/accessories', [AdminControllerSatu::class, 'accessoriesStore'])->name('accessories.store');
    Route::get('/accessories/{id}/edit', [AdminControllerSatu::class, 'accessoriesEdit'])->name('accessories.edit');
    Route::put('/accessories/{id}', [AdminControllerSatu::class, 'accessoriesUpdate'])->name('accessories.update');
    Route::delete('/accessories/{id}', [AdminControllerSatu::class, 'accessoriesDelete'])->name('accessories.delete');

    // Parts
    Route::post('/parts', [AdminControllerSatu::class, 'partsStore'])->name('parts.store');
    Route::get('/parts/{id}/edit', [AdminControllerSatu::class, 'partsEdit'])->name('parts.edit');
    Route::put('/parts/{id}', [AdminControllerSatu::class, 'partsUpdate'])->name('parts.update');
    Route::delete('/parts/{id}', [AdminControllerSatu::class, 'partsDelete'])->name('parts.delete');

    // Apparels
    Route::post('/apparels', [AdminControllerSatu::class, 'apparelsStore'])->name('apparels.store');
    Route::get('/apparels/{id}/edit', [AdminControllerSatu::class, 'apparelsEdit'])->name('apparels.edit');
    Route::put('/apparels/{id}', [AdminControllerSatu::class, 'apparelsUpdate'])->name('apparels.update');
    Route::delete('/apparels/{id}', [AdminControllerSatu::class, 'apparelsDelete'])->name('apparels.delete');
    Route::get('/apparel-categories', [AdminControllerSatu::class, 'apparelCategoriesIndex'])->name('apparel-category.index');

    // Branches
    Route::post('/branches', [AdminControllerSatu::class, 'branchesStore'])->name('branches.store');
    Route::get('/branches/{id}/edit', [AdminControllerSatu::class, 'branchesEdit'])->name('branches.edit');
    Route::put('/branches/{id}', [AdminControllerSatu::class, 'branchesUpdate'])->name('branches.update');
    Route::delete('/branches/{id}', [AdminControllerSatu::class, 'branchesDelete'])->name('branches.delete');

    // Banner Template Routes
    Route::get('/banner/templates', [AdminControllerSatu::class, 'manageBannerTemplates'])->name('banner.templates');
    Route::get('/banner/template/{id?}', [AdminControllerSatu::class, 'manageBannerTemplate'])->name('banner.template.manage');
    Route::post('/banner/template/store', [AdminControllerSatu::class, 'storeBannerTemplate'])->name('banner.template.store');
    Route::put('/banner/template/edit', [AdminControllerSatu::class, 'editBannerTemplate'])->name('banner.template.edit');
    Route::delete('/banner/template/delete', [AdminControllerSatu::class, 'deleteBannerTemplate'])->name('banner.template.delete');

    // Banner Routes
    Route::get('/banner/data', [AdminControllerSatu::class, 'getBannerData'])->name('banner.data');
    Route::post('/banner/store', [AdminControllerSatu::class, 'storeBanners'])->name('banner.store');
    Route::put('/banner/edit', [AdminControllerSatu::class, 'editBanner'])->name('banner.edit');
    Route::delete('/banner/delete', [AdminControllerSatu::class, 'deleteBanner'])->name('banner.delete');
    Route::post('/banner/update-order', [AdminControllerSatu::class, 'updateBannerOrder'])->name('banner.update.order');

    // Test Ride Requests
    Route::get('/test-rides', [AdminControllerSatu::class, 'testRidesIndex'])->name('test-rides.index');
    Route::get('/test-rides/{id}', [AdminControllerSatu::class, 'testRidesShow'])->name('test-rides.show');
    Route::delete('/test-rides/{id}', [AdminControllerSatu::class, 'testRidesDelete'])->name('test-rides.delete');

    // Credit Simulation Requests
    Route::get('/credits', [AdminControllerSatu::class, 'creditsIndex'])->name('credits.index');
    Route::get('/credits/{id}', [AdminControllerSatu::class, 'creditsShow'])->name('credits.show');
    Route::delete('/credits/{id}', [AdminControllerSatu::class, 'creditsDelete'])->name('credits.delete');

    Route::get('motor-categories', [AdminControllerSatu::class, 'motorCategoryIndex'])->name('motor-categories.index');
    Route::post('motor-categories/store', [AdminControllerSatu::class, 'storeMotorCategory'])->name('motor-categories.store');
    Route::post('motor-categories/update', [AdminControllerSatu::class, 'updateMotorCategory'])->name('motor-categories.update');
    Route::delete('motor-categories/delete', [AdminControllerSatu::class, 'destroyMotorCategory'])->name('motor-categories.destroy');
    Route::get('motor-categories/data', [AdminControllerSatu::class, 'getMotorCategories'])->name('motor-categories.data');

    Route::get('accessories-categories', [AdminControllerSatu::class, 'accessoriesCategoryIndex'])->name('accessories-categories.index');
    Route::post('accessories-categories/store', [AdminControllerSatu::class, 'storeAccessoriesCategory'])->name('accessories-categories.store');
    Route::post('accessories-categories/update', [AdminControllerSatu::class, 'updateAccessoriesCategory'])->name('accessories-categories.update');
    Route::delete('accessories-categories/delete', [AdminControllerSatu::class, 'destroyAccessoriesCategory'])->name('accessories-categories.destroy');
    Route::get('accessories-categories/data', [AdminControllerSatu::class, 'getAccessoriesCategories'])->name('accessories-categories.data');

    Route::get('apparel-categories', [AdminControllerSatu::class, 'apparelCategoryIndex'])->name('apparel-categories.index');
    Route::post('apparel-categories/store', [AdminControllerSatu::class, 'storeApparelCategory'])->name('apparel-categories.store');
    Route::post('apparel-categories/update', [AdminControllerSatu::class, 'updateApparelCategory'])->name('apparel-categories.update');
    Route::delete('apparel-categories/delete', [AdminControllerSatu::class, 'destroyApparelCategory'])->name('apparel-categories.destroy');
    Route::get('apparel-categories/data', [AdminControllerSatu::class, 'getApparelCategories'])->name('apparel-categories.data');

    Route::get('parts-categories', [AdminControllerSatu::class, 'partsCategoryIndex'])->name('parts-categories.index');
    Route::post('parts-categories/store', [AdminControllerSatu::class, 'storePartsCategory'])->name('parts-categories.store');
    Route::post('parts-categories/update', [AdminControllerSatu::class, 'updatePartsCategory'])->name('parts-categories.update');
    Route::delete('parts-categories/delete', [AdminControllerSatu::class, 'destroyPartsCategory'])->name('parts-categories.destroy');
    Route::get('parts-categories/data', [AdminControllerSatu::class, 'getPartsCategories'])->name('parts-categories.data');
});