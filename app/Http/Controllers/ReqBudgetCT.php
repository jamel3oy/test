<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReqBudgetCT extends Controller
{
    public function getFacultyAll(Request $req)
    {
        $facultyid = $req->facultyid;
        $statususe = $req->statususe;
        $sql = "SELECT sd.DEPARTMENTID AS FACULTYID, sd.DEPARTMENTNAME AS FACULTYNAME FROM SYS_DEPARTMENT sd WHERE sd.DEPARTMENTSTATUS IS NOT NULL OR sd.REMARK = 1 ORDER BY sd.DEPARTMENTID ASC";
        if ($statususe == "09")
        {
            $sql = "SELECT sd.DEPARTMENTID AS FACULTYID, sd.DEPARTMENTNAME AS FACULTYNAME FROM SYS_DEPARTMENT sd WHERE (sd.DEPARTMENTSTATUS IS NOT NULL OR sd.REMARK = 1) AND sd.FACULTYID = '".$facultyid."' ORDER BY sd.DEPARTMENTID ASC";
        } 

        return DB::connection("msumis")->select($sql);
    }

    public function getReqBudget8900(Request $req)
    {
        $facInq = strlen($req->facultyid) > 7 ? (substr($req->facultyid,0,7)=="2010927" ? substr($req->facultyid,0,9):substr($req->facultyid,0,7)) : null;
        if($facInq != null){
            $sql = "SELECT pr.BUDGETYEAR , pr.ORGANIZATIONID , pr.PROJECTID , sp.PROJECTWORKNAME, pr.EXPENSESID , pr2.GBL1ID , pp.NAME AS GBL1NAME,
                    pr2.DETAILNAME , pr2.AMOUNT , pma.AIM_NAME , pmi.IND_NAME , pr.REQUESTBUDGETID , pr2.REQUESTDETAILID 
                FROM PBUD_REQUESTBUDGET pr 
                LEFT JOIN PBUD_REQUESTBUDGETDETAIL pr2 ON pr2.REQUESTBUDGETID = pr.REQUESTBUDGETID 
                LEFT JOIN PBUD_MSUPLAN_AIM pma ON pma.AIM_ID = pr2.AIM_ID AND pma.BUDGETYEAR = pr.BUDGETYEAR 
                LEFT JOIN PBUD_MSUPLAN_IND pmi ON pmi.IND_ID = pr2.IND_ID AND pmi.AIM_ID = pma.AIM_ID AND pmi.BUDGETYEAR = pr.BUDGETYEAR 
                LEFT JOIN PPLN_PLANTYPE pp ON pp.ID = pr2.GBL1ID 
                LEFT JOIN SYS_PROJECTWORK sp  ON sp.PROJECTWORKID = pr.PROJECTID 
                WHERE pr.BUDGETYEAR = $req->budgetyear 
                AND pr.ORGANIZATIONID LIKE '".$facInq."%'
                AND pr.SOURCE = 'Genaral'
                AND pr.EXPENSESID IN (800,900)
                AND pr.PROJECTID != 4101
                AND pr2.STATUSCHK != 'F'
                ORDER BY pr2.GBL1ID, pr.PROJECTID ASC";
            $res = DB::connection("msumis")->select($sql);
            $rex = [];
            foreach ($res as $k => $v) {
                $v->output = DB::connection("msumis")->table("PBUD_REQUESTDETAILOUTPUT")->where("REQUESTDETAILID", $v->requestdetailid)->get();
                $v->outcome = DB::connection("msumis")->table("PBUD_REQUESTDETAILOUTCOME")->where("REQUESTDETAILID", $v->requestdetailid)->get();
                $rex[$k]=$v;
            }
            return $res;
        }else return [];
    }
}
