<table id="table" data-toggle="table" data-pagination="true" data-search="true" data-show-columns="true" data-show-pagination-switch="true" data-show-refresh="true" data-key-events="true" data-show-toggle="true" data-resizable="true" data-cookie="true"data-cookie-id-table="saveId" data-show-export="true" data-click-to-select="true" data-toolbar="#toolbar">
   <thead>   
   <tr align='center'>

        <th> # </th>
        <th>Staff Name</th>
		 <th>Designation</th>
		 <th>Department</th>
         <th>Subdepartment</th>
        <?php for($i=1;$i<32;$i++){ ?>
            <th style="text-align:center;"><?php echo $i;?></th>
        <?php }?>
    </tr>
	 </thead>
    <?php $count = 1; ?>
	<tbody>
    @foreach($staffs as $staff)
        <tr align="center">
            <td>{{$count++}}</td>
            <td>{{$staff->StaffFullName}}</td>
			<td>{{$staff->Assignment}}</td>
			<td>{{$staff->Department}}</td>
            <td>{{$staff->Subdepartment}}</td>
            @foreach($dateRange as $date)
               <?php if($staffAttendance[$staff->StaffNum][$date->format("Y-m-d")]=='P'|| $staffAttendance[$staff->StaffNum][$date->format("Y-m-d")]=='A' || $staffAttendance[$staff->StaffNum][$date->format("Y-m-d")]=='H'||$staffAttendance[$staff->StaffNum][$date->format("Y-m-d")]=='L'||$staffAttendance[$staff->StaffNum][$date->format("Y-m-d")]=='Sat'||$staffAttendance[$staff->StaffNum][$date->format("Y-m-d")]=='Sun'||$staffAttendance[$staff->StaffNum][$date->format("Y-m-d")]=='1/2 day L')
                {
                   echo '<td>'.$staffAttendance[$staff->StaffNum][$date->format("Y-m-d")].'</td>';
               }else{
                echo "<td style='background-color: red;'><div style='background-color: red;'>". $staffAttendance[$staff->StaffNum][$date->format("Y-m-d")].'</div></td>';
                }
                
                ?>
            @endforeach
        </tr>
    @endforeach
	</tbody>
</table>