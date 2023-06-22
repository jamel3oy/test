<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth.gateway')->get('/user', function (Request $request) {
    return Auth::user();
});
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     $staffdepartment_default = DB::select("SELECT FD_FACNAME(?) AS STAFFDEPARTMENTNAME FROM DUAL", [$request->user()->staffdepartment]);
//     $accProgramGroup = array(
//         "progcode" => "",
//         "groupid" => "",
//         "groupname" => "",
//         "staffdepartment" => $request->user()->staffdepartment,
//         "staffdepartmentname" => count($staffdepartment_default) > 0 ? $staffdepartment_default[0]->staffdepartmentname : "Cannot find positionname.",
//     );
//     if(!empty($request->progcode)){
//         $accProgramGroup = DB::table('mgn_userlogin')->leftJoin('mgn_groupuser', 'mgn_groupuser.groupid', 'mgn_userlogin.groupid')
//             ->where('mgn_userlogin.userid', $request->user()->staffcitizenid)
//             ->where('mgn_userlogin.progcode', $request->progcode)
//             ->selectRaw("mgn_userlogin.progcode, mgn_userlogin.groupid, mgn_groupuser.groupname, mgn_userlogin.workdepartmentid as staffdepartment, FD_FACNAME(mgn_userlogin.workdepartmentid) AS STAFFDEPARTMENTNAME")
//             ->first();
//     }
//     $prefixname = DB::table('STF_PREFIX')->where("PREFIXID",$request->user()->prefixid)->first("PREFIXFULLNAME");
//     $positionname = DB::select("SELECT STFPOSNAME(?) AS POSITIONNAME FROM DUAL", [$request->user()->posid]);
//     $resUser = array(
//         "STAFFID" => $request->user()->staffid,
//         "PREFIXID" => $request->user()->prefixid,
//         "STAFFNAME" => $request->user()->staffname,
//         "STAFFSURNAME" => $request->user()->staffsurname,
//         "GENDERID" => $request->user()->genderid,
//         "STAFFEMAIL1" => $request->user()->staffemail1,
//         "STAFFEMAIL2" => $request->user()->staffemail2,
//         "POSID" => $request->user()->posid,
//         "STAFFFACULTY" => $request->user()->stafffaculty,
//         "POSTYPEID" => $request->user()->postypeid,
//         "SCOPES" => $accProgramGroup,
//         "PREFIXFULLNAME" => $prefixname->prefixfullname,
//         "POSITIONNAME" => count($positionname) > 0 ? $positionname[0]->positionname : "Cannot find positionname.",
//     );
//     return json_encode($resUser);
// });

Route::post('createcontract', 'WordsToPdf@createContract');

Route::post('sign', 'WordsToPdf@sign');

Route::post('testloop', 'WordsToPdf@testLoop');
Route::post('signx', 'SignPdf@sign');

Route::get('getfacall', 'ReqBudgetCT@getFacultyAll');

Route::get('getalldata', 'ReqBudgetCT@getReqBudget8900');
