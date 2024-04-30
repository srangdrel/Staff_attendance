<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendApproveLeaveMail;
use App\Mail\LeaveRequestMail;

use Illuminate\Http\Request;
use DateTime;
use DatePeriod;
use DateInterval;
use App;
use DB;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session_start();
                     
        return view('leave');
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

       
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
       $this->validate($request, [
            'reason'   => 'required',
            'file' => 'max:2048',
           ]);



    

  

        
      
       session_start();

         
         


        
       $date_from = strtotime($request->input('start')); 
       $date_to = strtotime($request->input('end')); 
	   $diff = $date_to - $date_from;
       if (round($diff / 86400)>5){

        return redirect()->route('leave.index')->with('sucess', 'Not allowed more then 5 days');
       }
	   
	    if($date_from>$date_to){
		   return redirect()->route('leave.index')->with('sucess', 'From date should be less than to date');
	   }
       $findleave=DB::table('tblLeaves')->where('Staff',$_SESSION["staff"])
       ->where('FromDate',$request->input('start'))
       ->where('ToDate',$request->input('end'))
       ->first();
         
      if($findleave==!NULL){
           return redirect()->route('leave.index')->with('sucess', 'Already Request the leave');

        }     
      
       $diff = $date_to - $date_from;
       $count=1;
       $count1=0.5;
       $count2=1;
       $satwrk=0;
       
       

       $finsatwrk=DB::table('tblStaff')->where('StaffNum',$_SESSION["staff"])->first();
       if($finsatwrk->WorksOnSaturday==1){
        $satwrk=1;
       }
       
       for ($i=$date_from; $i<=$date_to; $i+=86400) {
                                   $holiday=DB::table('tblStaffAttendance')
                                             ->where('Status','H')
                                             ->where('Year',date('Y',$i ))
                                             ->where('Month',date('m',$i ))
                                             ->where('Day',date('d',$i ))
                                             ->get();
                              
                                     if(count($holiday)==0){

                                        $wintertiming1=DB::table('vwWEBCurrentSemester')
       
       
      
                                         ->first();
        
                                         $wintertiming=DB::table('tlkpSemesters')
                                          ->where('SemesterNum',$wintertiming1->CurrentSem)
                                            ->first();
                                        if($wintertiming->FallSpring=='Fall'&& $wintertiming->EndDate < date('Y-m-d')||$wintertiming->FallSpring=='Spring'&& $wintertiming->StartDate > date('Y-m-d')){
                                            if(date("w", $i)!=0 &&date("w", $i)!=6){
           
        
                                                ++$count;
                                                 
                                               }
                                        }else{   
                                              
                                                if($satwrk==1)
                                                  {

                                                    if(date("w", $i)!=0){
                                                        ++$count;
                                             
                                                
                                                
                                                   
                                                       }
                                              
                                                       if(date("w", $i)==6){
                                                       $count1+=0.5;
                                              
                                                      

                                               
                                                       }


                                                  }else{
                                                              if(date("w", $i)!=0 && date("w", $i)!=6){
                                                                   ++$count;
                                             
                                                
                                                
                                                   
                                                                  }
                                                                   $satwd=DB::table('tblStaffAttendance')->where('Status','sw')->get();
                                                                      foreach($satwd as $sat) { 
                                                                         
                                                                          if($i==strtotime($sat->Year."-".$sat->Month."-".$sat->Day)){
                                                                            $count1+=0.5;
                                                   
                                                                             $count+=1;
     
                                                    
                                                                           }
                                                                      }
                                                                    
                                                  
                                                
                                                  }                                          
                                                   
                                                  
                                                            

                                               
                                      }       
                                 }

           
              
         
    }
   
    $shalfday=0;
        $ehalfday=0;
        if($request->input('shalfday')=='1'){
            $shalfday=0.5;
        }
        if($request->input('ehalfday')=='1'){
            $ehalfday=0.5;
        }

        $staff=DB::table('tblStaff')->where('StaffNum',$_SESSION["staff"])->first();
        if($request->input('leave')=="cl"){
            if($staff->CasualLeaveAccrued>=(($count-1)-($count1-0.5))-$shalfday-$ehalfday){
                
                DB::table('tblLeaves')->insert(
                ['Staff' => $_SESSION["staff"], 'FromDate' => date('Y-m-d H:i:s.000',strtotime($request->input('start'))),'ToDate'=>date('Y-m-d H:i:s.000',strtotime($request->input('end'))),'Reason'=>$request->input('reason'),'CasualLeave'=>(($count-1)-($count1-0.5))-$shalfday-$ehalfday,'Remarks'=>'Pending','Enterer'=>$_SESSION["staff"],'EntryDate'=>Date('Y-m-d H:i:s.000'),'Status'=>4]

            );
           
            }else{
                return redirect()->route('leave.index')->with('sucess', 'No casual leave balanace');
            }
        }
        if($request->input('leave')=="el"){
             if($staff->EarnedLeaveAccrued>=(($count-1)-($count1-0.5))-$shalfday-$ehalfday){
              

                    DB::table('tblLeaves')->insert(
                        ['Staff' => $_SESSION["staff"], 'FromDate' => date('Y-m-d H:i:s.000',strtotime($request->input('start'))),'ToDate'=>date('Y-m-d H:i:s.000',strtotime($request->input('end'))),'Reason'=>$request->input('reason'),'EarnedLeave'=>(($count-1)-($count1-0.5))-$shalfday-$ehalfday,'Remarks'=>'Pending','Enterer'=>$_SESSION["staff"],'EntryDate'=>Date('Y-m-d H:i:s.000'),'Status'=>4]

                    );
                 
              
                
            }else{
                return redirect()->route('leave.index')->with('sucess', 'No earned leave balanace');
            }
        }
		
		 if($request->input('leave')=="comp"){
                if($staff->CompLeaveAccrued>=(($count-1)-($count1-0.5))-$shalfday-$ehalfday){
                  
   
                      DB::table('tblLeaves')->insert(
                           ['Staff' => $_SESSION["staff"], 'FromDate' => date('Y-m-d H:i:s.000',strtotime($request->input('start'))),'ToDate'=>date('Y-m-d H:i:s.000',strtotime($request->input('end'))),'Reason'=>$request->input('reason'),'CompLeave'=>(($count-1)-($count1-0.5))-$shalfday-$ehalfday,'Remarks'=>'Pending','Enterer'=>$_SESSION["staff"],'EntryDate'=>Date('Y-m-d H:i:s.000'),'Status'=>4]
   
                       );
                       
                 
                   
               }else{
                   return redirect()->route('leave.index')->with('sucess', 'No comp leave balanace');
               }
            }
     
                                                                                       
            
                                                                                         
                                                                                 
                                                                              
                                                                             $checksubdephead= $getSubdprtHead=DB::table('tlkpSubdepartments')->where('SubdepartmentHead',$_SESSION["staff"])->first();
                                                                              
                                                                              $getStaffD=DB::table('tblStaff')->where('StaffNum',$_SESSION["staff"])->first();
                                                                              $checkdephead=DB::table('tlkpDepartments')->where('DepartmentHead',$_SESSION["staff"])->first();
                                                                                    if($checkdephead==Null){
                                                                                        $getdprtHead=DB::table('tlkpDepartments')->where('DepartmentNum',$getStaffD->Department)->first();
																						          
																								  
																								  
																									if($getStaffD->AssignmentNum=='101'){
                                                                                                    
                                                                                                        Mail::to('dechendolkar@rtc.bt')->send(new LeaveRequestMail());
                                                                                                       
                                                                                                     }
																								   elseif($getStaffD->AssignmentNum=='38'||$getStaffD->AssignmentNum=='64'||$getStaffD->StaffNum=='830'||$getStaffD->AssignmentNum=='115'){
                                                                                                        // echo "pl";
                                                                                                        Mail::to('sdorji@rtc.bt')->send(new LeaveRequestMail());
                                                                                                     
                                                                                                   }
                                                                                                  
                                                                                                   elseif($getStaffD->AssignmentNum=='181'||$getStaffD->AssignmentNum=='115'){
																								   //||$getStaffD->AssignmentNum=='134'||$getStaffD->AssignmentNum=='115'||$getStaffD->AssignmentNum=='25'){
                                                                                                   
                                                                                                        Mail::to('bikash@rtc.bt')->send(new LeaveRequestMail());
                                                                                                    
                                                                                                  }
                                                                                                
                                                                                                  elseif($getStaffD->AssignmentNum=='15'){
                                                                                                            //  echo"444";
                                                                                                              $getDprtHead=DB::table('tlkpDepartments')->where('DepartmentNum',$getStaffD->Department)->first();
                                                                                  
                                                                                  
                                                                                                              $getSupervisorMail=DB::table('tblStaff')->where('StaffNum',$getDprtHead->DepartmentHead)->first();
                                                                                                            //  echo $getSupervisorMail->EMailRTC;
                                                                                                             Mail::to($getSupervisorMail->EMailRTC.'@rtc.bt')->send(new LeaveRequestMail());
                                                                                                  }
                                                                                                 elseif($checksubdephead==Null){
                                                                                  
                                                                                                         
                                                                                  
                                                                                                        $getSubdprtHead=DB::table('tlkpSubdepartments')->where('SubdepartmentNum',$getStaffD->Subdepartment)->first();
                                                                                  
                                                                                  
                                                                                                        $getSupervisorMail=DB::table('tblStaff')->where('StaffNum',$getSubdprtHead->SubdepartmentHead)->first();
                                                                                                      //echo $getSupervisorMail->EMailRTC;
                                                                                                                     // echo $getSupervisorMail->StaffNum;
                                                                                                        Mail::to($getSupervisorMail->EMailRTC.'@rtc.bt')->send(new LeaveRequestMail());
                                                                                
                                                                                                    } 
                                                                                                else{
                                                                                                   
                                                                                                   
																									
																									
																									  if($_SESSION["AssignmentNum"]=='97'){
                                                                                                       
                                                                                                         $getSupervisorMail=DB::table('tblStaff')->where('AssignmentNum','15')->first();
                                                                                                         //echo $getSupervisorMail->EMailRTC;
																										 Mail::to($getSupervisorMail->EMailRTC.'@rtc.bt')->send(new LeaveRequestMail());
                                                                                                     }else{
                                                                                                        $getDprtHead=DB::table('tlkpDepartments')->where('DepartmentNum',$getStaffD->Department)->first();
                                                                                                        $getSupervisorMail=DB::table('tblStaff')->where('StaffNum',$getDprtHead->DepartmentHead)->first();
                                                                                                         //echo $getSupervisorMail->EMailRTC;
																										
																										Mail::to($getSupervisorMail->EMailRTC.'@rtc.bt')->send(new LeaveRequestMail());
                                                                                                    }
                                                                                                    }
                                                                                         }else{
                                                                                            $getHeadMail=DB::table('tblStaff')->where('AssignmentNum','87')->first();

                                                                                              //echo $getHeadMail->EMailRTC;
                                                                                              Mail::to($getHeadMail->EMailRTC.'@rtc.bt')->send(new LeaveRequestMail());
                                                                                  
                                                                                 
                                                                                                   
                                                                                        }
                                                                    
                                                                         
                                                                     ///}
                                                                   return redirect()->route('leave.index')->with('sucess', 'Leave request sent for ')->with('noOfdays',(($count-1)-($count1-0.5))-$shalfday-$ehalfday);
                                                                      
    
        
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
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}
