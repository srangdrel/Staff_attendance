<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

Use DB;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        session_start();
      return view('ListSupervisor1');
     
        
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
        session_start();
      
      
                    if($_SESSION["AssignmentNum"]=='25'){
            $leave=DB::table('tblleaves')
            ->join('tblStaff','Staff','=','StaffNum')
           // ->join('tlkpSubdepartments','Staff','=','SubdepartmentHead')
             ->where('Department',$id)
             
             ->whereNotIn('StaffNum',[$_SESSION["staff"]])
             ->whereIn('AssignmentNum',['64','38','115'])
             ->where('Status','4')
             //->unique('StaffNum')
             ->get();
            /* //echo "000";
             foreach($leave as $l){
                echo $l->Staff."<br>";
             }*/

          }
          elseif($_SESSION["AssignmentNum"]=='100'){
            $leave=DB::table('tblleaves')
            ->join('tblStaff','Staff','=','StaffNum')
           
             
             ->whereIn('AssignmentNum',['38','44','25','134','181','180'])
             ->where('Status','4')
             //->unique('StaffNum')
             ->get();

          }
		   elseif($_SESSION["AssignmentNum"]=='60'){
            $leave=DB::table('tblleaves')
            ->join('tblStaff','Staff','=','StaffNum')
           
           
            ->whereNotIn('StaffNum',[$_SESSION["staff"]])
           ->whereIn('AssignmentNum',['101','168','175'])
           
            ->where('Status','4')
            ->get();


          }
         else{
            $leave=DB::table('tblleaves')
            ->join('tblStaff','Staff','=','StaffNum')
            ->join('tlkpSubdepartments','Staff','=','SubdepartmentHead')
             ->where('Department',$id)
             ->whereNotIn('StaffNum',[$_SESSION["staff"]])
             ->where('Status','4')
             //->unique('StaffNum')
             ->get();

          }
      

         

               return view('ListLeave1',['leave' => $leave]);
              

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $findstaff=DB::table('tblLeaves')->where('LeaveNum',$id)->first();
        echo  $findstaff->Staff;
       if ($findstaff->CasualLeave==!NULL){
   
           DB::table('tblStaff')->where('StaffNum',$findstaff->Staff)->decrement('CasualLeaveAccrued',$findstaff->CasualLeave);
          
       }
         if($findstaff->EarnedLeave==!NULL){
           DB::table('tblStaff')->where('StaffNum',$findstaff->Staff)->decrement('EarnedLeaveAccrued',$findstaff->EarnedLeave);
         }
   
         DB::table('tblleaves')
              ->where('LeaveNum', $id)
            ->update(['Status' => 1]);
        DB::table('tblleaves')
        ->where('LeaveNum', $id)
        ->update(['Status' => 1]);

       /* Mail::send('tickets.emails.tickets',array('ticketsCurrentNewId'=>
'hhjj','ticketsCurrentSubjectId'=>'hhjkkk','ticketsCurrentLocationsObj'=>'jjkkk'), function($message)
{
//$message->from('your@gmail.com');
$message->to('sherubrangdrel@rtc.bt', 'Amaresh')->subject(`Welcome!`);
});*/
    
    return back()->with('success', 'approved');

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
        DB::table('tblLeaves')->where('leaveNum', $id)->delete();
        return back()->with('success', 'Deleted');
    }
}
