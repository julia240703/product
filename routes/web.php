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
Route::get('/motors/category/{name}', [PublicControllerSatu::class, 'motorsByCategory'])->name('motors.by.category');
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

    // Motor base
    Route::get('/motors', [AdminControllerSatu::class, 'motorsIndex'])->name('motors.index');
    Route::get('/motors/published', [AdminControllerSatu::class, 'motorsPublished'])->name('motors.published');
    Route::get('/motors/unpublished', [AdminControllerSatu::class, 'motorsUnpublished'])->name('motors.unpublished');
    Route::post('/motors/store', [AdminControllerSatu::class, 'motorsStore'])->name('motors.store');
    Route::put('/motors/{id}', [AdminControllerSatu::class, 'updateMotor'])->name('motors.update');
    Route::delete('/motors/{id}', [AdminControllerSatu::class, 'deleteMotor'])->name('motors.delete');
    Route::get('/get-types/{category_id}', [AdminControllerSatu::class, 'getTypesByCategory'])->name('motors.getTypes');

    // Warna (per motor)
    Route::get('/{motor}/colors', [AdminControllerSatu::class, 'colorsIndex'])->name('colors.index');
    Route::post('/{motor}/colors', [AdminControllerSatu::class, 'colorsStore'])->name('colors.store');
    Route::get('/{motor}/colors/{id}/edit', [AdminControllerSatu::class, 'colorsEdit'])->name('colors.edit');
    Route::put('/{motor}/colors/{id}', [AdminControllerSatu::class, 'colorsUpdate'])->name('colors.update');
    Route::delete('/{motor}/colors/{id}', [AdminControllerSatu::class, 'colorsDelete'])->name('colors.delete');

    // Accessories
    Route::get('/{motor}/accessories', [AdminControllerSatu::class, 'accessoriesIndex'])->name('accessories.index');
    Route::post('/{motor}/accessories', [AdminControllerSatu::class, 'accessoriesStore'])->name('accessories.store');
    Route::get('/{motor}/accessories/{id}/edit', [AdminControllerSatu::class, 'accessoriesEdit'])->name('accessories.edit');
    Route::put('/{motor}/accessories/{id}', [AdminControllerSatu::class, 'accessoriesUpdate'])->name('accessories.update');
    Route::delete('/{motor}/accessories/{id}', [AdminControllerSatu::class, 'accessoriesDelete'])->name('accessories.delete');

    // Spareparts
    Route::get('/{motor}/spareparts', [AdminControllerSatu::class, 'sparepartsIndex'])->name('spareparts.index');
    Route::post('/{motor}/spareparts', [AdminControllerSatu::class, 'sparepartsStore'])->name('spareparts.store');
    Route::get('/{motor}/spareparts/{id}/edit', [AdminControllerSatu::class, 'sparepartsEdit'])->name('spareparts.edit');
    Route::put('/{motor}/spareparts/{id}', [AdminControllerSatu::class, 'sparepartsUpdate'])->name('spareparts.update');
    Route::delete('/{motor}/spareparts/{id}', [AdminControllerSatu::class, 'sparepartsDelete'])->name('spareparts.delete');

    // Spesifikasi
    Route::get('/{motor}/specifications', [AdminControllerSatu::class, 'specificationsIndex'])->name('specifications.index');
    Route::post('/{motor}/specifications', [AdminControllerSatu::class, 'specificationsStore'])->name('specifications.store');
    Route::get('/{motor}/specifications/{id}/edit', [AdminControllerSatu::class, 'specificationsEdit'])->name('specifications.edit');
    Route::put('/{motor}/specifications/{id}', [AdminControllerSatu::class, 'specificationsUpdate'])->name('specifications.update');
    Route::delete('/{motor}/specifications/{id}', [AdminControllerSatu::class, 'specificationsDelete'])->name('specifications.delete');

    // Features
    Route::get('/{motor}/features', [AdminControllerSatu::class, 'featuresIndex'])->name('features.index');
    Route::post('/{motor}/features', [AdminControllerSatu::class, 'featuresStore'])->name('features.store');
    Route::put('/{motor}/features/{id}', [AdminControllerSatu::class, 'featuresUpdate'])->name('features.update');
    Route::delete('/{motor}/features/{id}', [AdminControllerSatu::class, 'featuresDelete'])->name('features.delete');

    // Apparels
    Route::get('apparels', [AdminControllerSatu::class, 'apparelsIndex'])->name('apparels.index');
    Route::post('/apparels', [AdminControllerSatu::class, 'apparelsStore'])->name('apparels.store');
    Route::get('/apparels/{id}/edit', [AdminControllerSatu::class, 'apparelsEdit'])->name('apparels.edit');
    Route::put('/apparels/{id}/update', [AdminControllerSatu::class, 'apparelsUpdate'])->name('apparels.update');
    Route::delete('/apparels/delete/{id}', [AdminControllerSatu::class, 'apparelsDelete'])->name('apparels.delete');
    Route::get('/apparels/data', [AdminControllerSatu::class, 'apparelsData'])->name('apparels.data');

    // Branches
    Route::get('branches', [AdminControllerSatu::class, 'branchesIndex'])->name('branches.index');
    Route::post('/branches/store', [AdminControllerSatu::class, 'branchesStore'])->name('branches.store');
    Route::get('/branches/{id}/edit', [AdminControllerSatu::class, 'branchesEdit'])->name('branches.edit');
    Route::put('/branches/{id}/update', [AdminControllerSatu::class, 'branchesUpdate'])->name('branches.update');
    Route::delete('/branches/delete/{id}', [AdminControllerSatu::class, 'branchesDelete'])->name('branches.delete');
    Route::get('/branches/data', [AdminControllerSatu::class, 'getBranchesData'])->name('admin.branches.data');
    Route::post('/branches/update-order', [AdminControllerSatu::class, 'updateBranchOrder'])->name('branches.updateOrder');

    // Banner Template Management Routes
    Route::get('/banner', [AdminControllerSatu::class, 'adminbanner'])->name('banner');
    
    // Template CRUD - untuk AJAX requests
    Route::get('/template/manage', [AdminControllerSatu::class, 'manageBannerTemplate'])->name('template.manage');
    Route::get('/template/manage/{id}', [AdminControllerSatu::class, 'manageBannerTemplate'])->name('template.show');
    Route::post('/template/store', [AdminControllerSatu::class, 'storeBannerTemplate'])->name('template.store');
    Route::post('/template/edit', [AdminControllerSatu::class, 'editBannerTemplate'])->name('template.edit');
    Route::post('/template/delete', [AdminControllerSatu::class, 'deleteBannerTemplate'])->name('template.delete');
    
    // Banner CRUD - terpisah dari template
    Route::get('/manage/{templateId?}', [AdminControllerSatu::class, 'manageBanner'])->name('manage');
    Route::post('/store', [AdminControllerSatu::class, 'storeBanner'])->name('store');
    Route::post('/edit', [AdminControllerSatu::class, 'editBanner'])->name('edit');
    Route::post('/delete', [AdminControllerSatu::class, 'deleteBanner'])->name('delete');
    Route::post('/update-order', [AdminControllerSatu::class, 'updateBannerOrder'])->name('updateOrder');

    // Test Ride Requests
    Route::get('/test-rides', [AdminControllerSatu::class, 'testRidesIndex'])->name('test-rides.index');
    Route::get('/test-rides/{id}', [AdminControllerSatu::class, 'testRidesShow'])->name('test-rides.show');
    Route::delete('/test-rides/{id}', [AdminControllerSatu::class, 'testRidesDelete'])->name('test-rides.delete');

    // Credit Simulation Requests
    Route::get('/credits', [AdminControllerSatu::class, 'creditsIndex'])->name('credits.index');
    Route::get('/credits/{id}', [AdminControllerSatu::class, 'creditsShow'])->name('credits.show');
    Route::delete('/credits/{id}', [AdminControllerSatu::class, 'creditsDelete'])->name('credits.delete');

    // Motor Type
    Route::get('motor-type', [AdminControllerSatu::class, 'motorTypeIndex'])->name('motor-type.index');
    Route::post('motor-type/store', [AdminControllerSatu::class, 'storeMotorType'])->name('motor-type.store');
    Route::post('motor-type/update', [AdminControllerSatu::class, 'updateMotorType'])->name('motor-type.update');
    Route::delete('motor-type/delete', [AdminControllerSatu::class, 'deleteMotorType'])->name('motor-type.delete');
    Route::get('motor-type/data', [AdminControllerSatu::class, 'getMotorType'])->name('motor-type.data');

    // Apparel Category
    Route::get('apparel-categories', [AdminControllerSatu::class, 'apparelCategoryIndex'])->name('apparel-categories.index');
    Route::post('apparel-categories/store', [AdminControllerSatu::class, 'storeApparelCategory'])->name('apparel-categories.store');
    Route::post('apparel-categories/update', [AdminControllerSatu::class, 'updateApparelCategory'])->name('apparel-categories.update');
    Route::delete('apparel-categories/delete', [AdminControllerSatu::class, 'deleteApparelCategory'])->name('apparel-categories.delete');
    Route::get('apparel-categories/data', [AdminControllerSatu::class, 'getApparelCategories'])->name('apparel-categories.data');

    // Area Cabang
    Route::get('/branch-areas', [AdminControllerSatu::class, 'branchAreaIndex'])->name('branch-areas.index');
    Route::get('/branch-areas/data', [AdminControllerSatu::class, 'getBranchAreaData'])->name('branch-areas.data');
    Route::post('/branch-areas/store', [AdminControllerSatu::class, 'storeBranchArea'])->name('branch-areas.store');
    Route::post('/branch-areas/update', [AdminControllerSatu::class, 'updateBranchArea'])->name('branch-areas.update');
    Route::delete('/branch-areas/delete', [AdminControllerSatu::class, 'deleteBranchArea'])->name('branch-areas.delete');

    // Kota Cabang
    Route::get('/branch-cities', [AdminControllerSatu::class, 'branchCityIndex'])->name('branch-cities.index');
    Route::get('/branch-cities/data', [AdminControllerSatu::class, 'getBranchCityData'])->name('branch-cities.data');
    Route::post('/branch-cities/store', [AdminControllerSatu::class, 'storeBranchCity'])->name('branch-cities.store');
    Route::post('/branch-cities/update', [AdminControllerSatu::class, 'updateBranchCity'])->name('branch-cities.update');
    Route::delete('/branch-cities/delete', [AdminControllerSatu::class, 'deleteBranchCity'])->name('branch-cities.delete');

    // Kategori
    Route::get('/categories/{type}/data', [AdminControllerSatu::class, 'categoryData'])->name('categories.data');
    Route::get('/categories/{type}', [AdminControllerSatu::class, 'categoryIndex'])->name('categories.index');
    Route::post('/categories/store', [AdminControllerSatu::class, 'storeCategory'])->name('categories.store');
    Route::post('/categories/update', [AdminControllerSatu::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/delete', [AdminControllerSatu::class, 'deleteCategory'])->name('categories.delete');

    // Credit Simulations
    Route::get('/credit-simulations', [AdminControllerSatu::class, 'creditSimulationIndex'])->name('credit_simulations.index');
    Route::post('/credit-simulations', [AdminControllerSatu::class, 'creditSimulationStore'])->name('credit_simulations.store');
    Route::put('/credit-simulations/{id}', [AdminControllerSatu::class, 'creditSimulationUpdate'])->name('credit_simulations.update');
    Route::delete('/credit-simulations/{id}', [AdminControllerSatu::class, 'creditSimulationDelete'])->name('credit_simulations.delete');

    // Credit Simulations
    Route::get('/price-lists', [AdminControllerSatu::class, 'priceListIndex'])->name('price_list.index');
    Route::post('/price-lists', [AdminControllerSatu::class, 'priceListStore'])->name('price_list.store');
    Route::put('/price-lists/{id}', [AdminControllerSatu::class, 'priceListUpdate'])->name('price_list.update');
    Route::delete('/price-lists/{id}', [AdminControllerSatu::class, 'priceListDelete'])->name('price_list.delete');

    // Price List 
    Route::get('/price-lists', [AdminControllerSatu::class, 'priceListIndex'])->name('price_list.index');
    Route::post('/price-lists', [AdminControllerSatu::class, 'priceListStore'])->name('price_list.store');
    Route::put('/price-lists/{id}', [AdminControllerSatu::class, 'priceListUpdate'])->name('price_list.update');
    Route::delete('/price-lists/{id}', [AdminControllerSatu::class, 'priceListDelete'])->name('price_list.delete');
});