<!doctype html>
<html class="no-js" lang="en">
@include('link.css')


<body>
    <!--[if lt IE 8]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
    <!-- Start Left menu area -->
    @include('hr.side_menu')
    <!-- End Left menu area -->
    <!-- Start Welcome area -->
    <div class="all-content-wrapper">
    @include('hr.header')
        
        <div class="product-sales-area mg-tb-30">
            <div class="container-fluid" >
                <div class="row" >
                    
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10" style="margin-left:90px;">
                        <div class="white-box analytics-info-cs mg-b-30 res-mg-b-40 res-mg-t-30 table-mg-t-pro-n tb-sm-res-d-n dk-res-t-d-n">
                            <h3 class="box-title" style="margin-left:200px;">Casual Leave Balance&nbsp;:&nbsp;<?php echo $_SESSION["CausalLeaveAccrued"];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Earned Leave Balance&nbsp;:&nbsp;<?php echo $_SESSION["EarnedLeaveAccrued"];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Comp Leave Balance&nbsp;:&nbsp;<?php echo $_SESSION["CompLeaveAccrued"];?></h3>
                          
                           
                           
                        </div>
                       
                       
                    </div>
                </div>
            </div>
        </div>
		
		
        <script>
       document.getElementById("getFname").onchange = function (e) {
       if (this.value == 1) {
         document.getElementById("admDivCheck").style.display="";
       }
        else if (this.value == 2) {
         document.getElementById("admDivCheck").style.display="";
       }
	   else {
         document.getElementById("admDivCheck").style.display="none";
      }
     };
        </script>
        @include('footer')
        
    </div>
    @include('link.js')
    
    
</body>

</html>