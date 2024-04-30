<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DateTime;
use DatePeriod;
use DateInterval;
use App;
use DB;
use Response;

class EmployeeAttendanceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        session_start();
        $year1 = $req->get('year');
     

        $date_formatted = date_create($year1."-01");
        $startDate = date_format($date_formatted,'Y-m-d');

        if ($req->get('year') == date('Y-m')) {
            $endDate = date('Y-m-d');

        } else {
            $endDate = date_format($date_formatted,"Y-m-t");
        }
        $endDate = date_add(date_create($endDate),date_interval_create_from_date_string("1 day"));

        $interval = new DateInterval("P1D");
        $dateRange = new DatePeriod(date_create($startDate),$interval,$endDate);
       
         $staffs=DB::table('tblStaff')
                     ->where('Canteach','0')
                     ->whereNotNull('EMailRTC')
                     ->whereNotIn('StaffNum',[$_SESSION["staff"]])
                     ->where('Subdepartment',$_SESSION["subdepartment"])
                   //  ->where('CanTeach','0')
                     ->join('tblStaffContracts', 'tblStaff.StaffNum', '=', 'tblStaffContracts.Staff')
                     ->join('tlkpAssignments', 'tblStaff.AssignmentNum', '=', 'tlkpAssignments.AssignmentNum')
                     ->where('ContractStatus','1')
                     //->where('StaffNum','1065')
                     ->orderBy('StaffName')
                     ->get(['StaffNum','Assignment','StaffName','WorksOnSaturday']);
       
        $staffAttendance = [];
        foreach($dateRange as $date):
            $currentDate = $date->format("Y-m-d");
            $currentDay = $date->format("D");
            foreach($staffs as $staff):
                $staffNum = $staff->StaffNum;
              // $attendanceQuery = DB::select("select Status,Punch from tblStaffAttendance where CONCAT(Year,'-',CASE WHEN LENGTH(MONTH)=1 THEN CONCAT('0',MONTH) ELSE MONTH END,'-',CASE WHEN LENGTH(DAY)=1 THEN CONCAT('0',DAY) ELSE DAY END) = ? and Staff = ?",[$currentDate,$staffNum]);
              $attendanceQuery = DB::select("select a.Month,a.Day,a.Status,a.Punch,a.PunchIn,a.PunchOut,a.ReasonIn,a.ReasonOut,a.SupervisorComment,a.LocationIn,a.LocationOut from tblStaffAttendance a join tblStaff b on a.Staff = b.StaffNum  where (CAST(Year AS varchar) + '-' + CAST((CASE WHEN LEN(Month) = 1 THEN '0' + Month ELSE Month END) AS varchar)  + '-' + CAST((CASE WHEN LEN(Day) = 1 THEN '0' + Day ELSE Day END) AS varchar)) = ? and Staff = ?",[$currentDate,$staffNum]);
                $attendanceQuery1 = DB::select("select Status,Punch from tblStaffAttendance where (CAST(Year AS varchar) + '-' + CAST((CASE WHEN LEN(Month) = 1 THEN '0' + Month ELSE Month END) AS varchar)  + '-' + CAST((CASE WHEN LEN(Day) = 1 THEN '0' + Day ELSE Day END) AS varchar)) = '$currentDate' and Staff ='0'");
               
               
                  
                    if(count($attendanceQuery)>0){
                        if ($attendanceQuery[0]->Status == 'L') {
                            if($attendanceQuery[0]->Punch == 'in'||$attendanceQuery[0]->Punch == 'out'){
                                $attendance = '1/2 day L';
                             } else{
                                $attendance = 'L';
                             }
                        }  if($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Month == 01 && $attendanceQuery[0]->Day<32){

                            if($attendanceQuery[0]->PunchIn < '9:15:59' && $attendanceQuery[0]->PunchOut > '15:45:00'){
                              $attendance='P';
                             }
                             else{
                                $attendance = "P\n".$attendanceQuery[0]->PunchIn."\n".$attendanceQuery[0]->PunchOut."\nExplanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\nSupervisor Comment:".$attendanceQuery[0]->SupervisorComment;
                               //$attendance='Z';
                             }

                             
                         }
                         elseif($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Month == 12 && $attendanceQuery[0]->Day>17){

                          if($attendanceQuery[0]->PunchIn < '9:15:59' && $attendanceQuery[0]->PunchOut > '15:45:00'){
                              $attendance='P';
                             }
                             else{
                                $attendance = "P\n".$attendanceQuery[0]->PunchIn."\n".$attendanceQuery[0]->PunchOut."\nExplanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\nSupervisor Comment:".$attendanceQuery[0]->SupervisorComment;
                               //$attendance='Z';
                             }
                         }
                         elseif($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Month == 02 && $attendanceQuery[0]->Day<15){

                          if($attendanceQuery[0]->PunchIn < '9:15:59' && $attendanceQuery[0]->PunchOut > '15:45:00'){
                              $attendance='P';
                             }
                             else{
                                $attendance = "P\n".$attendanceQuery[0]->PunchIn."\n".$attendanceQuery[0]->PunchOut."\nExplanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\nSupervisor Comment:".$attendanceQuery[0]->SupervisorComment;
                               //$attendance='Z';
                             }
                         }
                        elseif ($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Punch == 'in' && $attendanceQuery[0]->LocationIn == 1 && $attendanceQuery[0]->LocationOut === NULL) {
                            $attendance = "--\n".$attendanceQuery[0]->PunchIn."\n unresolved    Explanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\nSupervisor Comment:".$attendanceQuery[0]->SupervisorComment;
                        }

                        elseif ($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Punch == 'out' && $attendanceQuery[0]->LocationIn == 1 && $attendanceQuery[0]->LocationOut== 1 ) {
                
              
                     
                       
                            if(is_null($attendanceQuery[0]->ReasonIn)&&is_null($attendanceQuery[0]->ReasonOut)){
                                 $attendance='P';
                             }
                             
                             else{
                                $attendance = "P\n Sign In:".$attendanceQuery[0]->PunchIn."Sign Out:".$attendanceQuery[0]->PunchOut."\nExplanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\nSupervisor Comment:".$attendanceQuery[0]->SupervisorComment;
                             }       
                          
                     

               }
                            
                           elseif ($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Punch == 'in' && $attendanceQuery[0]->LocationIn == 2 && $attendanceQuery[0]->LocationOut == NULL) {
                               $attendance ="--\n".$attendanceQuery[0]->PunchIn."\n".$attendanceQuery[0]->PunchOut."\nExplanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\nSupervisor Comment:".$attendanceQuery[0]->SupervisorComment. "  Sign In : Out Campus";


                           } 
                           elseif ($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Punch == 'out' && $attendanceQuery[0]->LocationIn == 1 && $attendanceQuery[0]->LocationOut == 2) {
                               $attendance = "P\n".$attendanceQuery[0]->PunchIn."\n".$attendanceQuery[0]->PunchOut."\nExplanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\n Supervisor Comment:".$attendanceQuery[0]->SupervisorComment."\n Sign Out : Out Campus";

                           }
                           elseif ($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Punch == 'out' && $attendanceQuery[0]->LocationIn == 2 &&  $attendanceQuery[0]->LocationOut == 1) {
                                 $attendance = "P\n".$attendanceQuery[0]->PunchIn."\n".$attendanceQuery[0]->PunchOut."\nExplanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\nSupervisor Comment:".$attendanceQuery[0]->SupervisorComment."\n Sign In : Out Campus";
    
   
                            } 
                            elseif ($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Punch == 'out' && $attendanceQuery[0]->LocationIn == 2 && $attendanceQuery[0]->LocationOut== 2) {
                                 $attendance ="P ".$attendanceQuery[0]->PunchIn."\n".$attendanceQuery[0]->PunchOut."\nExplanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\nSupervisor Comment:".$attendanceQuery[0]->SupervisorComment."\n Sign In : Out Campus  Sign Out:Out Campus";




                            }
                        
                        elseif ($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Punch == 'out' && $attendanceQuery[0]->LocationIn == 1 && $attendanceQuery[0]->LocationOut=== NULL) {
                             $attendance ="P ".$attendanceQuery[0]->PunchIn."\n".$attendanceQuery[0]->PunchOut."\nExplanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\nSupervisor Comment:".$attendanceQuery[0]->SupervisorComment."\n Sign In : Out Campus  Sign Out:Out Campus";

                           // $attendance ="P";


                        }
                        elseif ($attendanceQuery[0]->Status == 'P' && $attendanceQuery[0]->Punch == 'out' && $attendanceQuery[0]->LocationIn == 2 && $attendanceQuery[0]->LocationOut=== NULL) {
                            $attendance ="P ".$attendanceQuery[0]->PunchIn."\n".$attendanceQuery[0]->PunchOut."\nExplanation:".$attendanceQuery[0]->ReasonIn." ".$attendanceQuery[0]->ReasonOut."\nSupervisor Comment:".$attendanceQuery[0]->SupervisorComment."\n Sign In : Out Campus  Sign Out:Out Campus";

                            //$attendance ="P";


                        }
                           
            
               }
                    
                    elseif(count($attendanceQuery1)>0){
                                
                        // $attendance = 'H';
    
                          if ($attendanceQuery1[0]->Status == 'H') {
                              $attendance = 'H';
                          } elseif ($attendanceQuery1[0]->Status == 'W' ) {
                              $attendance = '';
                          }
                          elseif ($attendanceQuery1[0]->Status == 'sw' ) {
                              $attendance = 'A';
                          }
                          
                      }
                    
                    else{
                        
                        $attendance = 'A';

                        if($staff->WorksOnSaturday==2){

                            if ($currentDay=="Sat"||$currentDay=="Sun") {
                                $attendance = $currentDay;
                            }
                           // echo $staff->StaffName."<br>";
                           
                        }
                        elseif($staff->WorksOnSaturday==1){
        
                            if ($currentDay=="Sun") {
                                $attendance = $currentDay;
                            }
                           // echo $staff->StaffName."<br>";
                           
                        }
                        
                           
                        
                        
                        
                        
                        
                       
                    }
                   
                    
                         

                //}
               $staffAttendance[$staffNum][$currentDate] = $attendance;
            endforeach;
        endforeach;

       return view('EmployeeAttendanceView')->with('staffAttendance', $staffAttendance)->with('staffs',$staffs)->with('dateRange', $dateRange);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
