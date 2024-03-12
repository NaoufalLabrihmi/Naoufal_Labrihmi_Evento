 @extends('admin.layout.main')
 @section('content')
 <style>
     .chart {
         display: flex;
         flex-direction: row;
         justify-content: space-around;
         align-items: flex-end;
         height: 300px;
         /* Adjust height as needed */
     }

     .bar {
         width: 100px;
         /* Adjust width as needed */
         background-color: blueviolet;
         /* Default bar color */
         text-align: center;
         color: black;
         font-size: 14px;
         padding: 5px;
         transition: height 0.5s ease;
     }

     .label {
         margin-top: 5px;
     }
 </style>

 <!-- Begin Page Content -->
 <div class="container-fluid">

     <!-- Page Heading -->
     <div class="d-sm-flex align-items-center justify-content-between mb-4">
         <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
     </div>

     <!-- Content Row -->
     <div class="row">

         <!-- Earnings (Monthly) Card Example -->
         <div class="col-xl-3 col-md-6 mb-4">
             <div class="card border-left-primary shadow h-100 py-2">
                 <div class="card-body">
                     <div class="row no-gutters align-items-center">
                         <div class="col mr-2">
                             <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                 Total Users</div>
                             <div class="h5 mb-0 font-weight-bold text-gray-800">
                                 {{ $totalUsers }}
                             </div>
                         </div>
                         <div class="col-auto">
                             <i class="fas fa-users fa-2x text-gray-300"></i>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

         <div class="col-xl-3 col-md-6 mb-4">
             <div class="card border-left-success shadow h-100 py-2">
                 <div class="card-body">
                     <div class="row no-gutters align-items-center">
                         <div class="col mr-2">
                             <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                 Total Organizers</div>
                             <div class="h5 mb-0 font-weight-bold text-gray-800">
                                 {{ $totalOrganizers }}
                             </div>
                         </div>
                         <div class="col-auto">
                             <i class="fas fa-users fa-2x text-gray-300"></i>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

         <!-- Earnings (Monthly) Card Example -->
         <div class="col-xl-3 col-md-6 mb-4">
             <div class="card border-left-success shadow h-100 py-2">
                 <div class="card-body">
                     <div class="row no-gutters align-items-center">
                         <div class="col mr-2">
                             <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                 Total Events</div>
                             <div class="h5 mb-0 font-weight-bold text-gray-800">
                                 {{ $totalEvents }}
                             </div>
                         </div>
                         <div class="col-auto">
                             <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <!-- Earnings (Monthly) Card Example -->
         <div class="col-xl-3 col-md-6 mb-4">
             <div class="card border-left-warning shadow h-100 py-2">
                 <div class="card-body">
                     <div class="row no-gutters align-items-center">
                         <div class="col mr-2">
                             <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                 Total Of Categories</div>
                             <div class="h5 mb-0 font-weight-bold text-gray-800">
                                 {{ $totalEventTypes }}
                             </div>
                         </div>
                         <div class="col-auto">
                             <i class="fa fa-list-alt fa-2x text-gray-300"></i>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

     </div>



     <div class="chart">
         <div class="bar" style="height: {{ $totalUsers }}px;">
             <div class="label">Total Users</div>
         </div>
         <div class="bar" style="height: {{ $totalOrganizers }}px;">
             <div class="label">Total Organizers</div>
         </div>
         <div class="bar" style="height: {{ $totalEvents }}px;">
             <div class="label">Total Events</div>
         </div>
         <div class="bar" style="height: {{ $totalEventTypes }}px;">
             <div class="label">Total Of Categories</div>
         </div>
     </div>









 </div>









 @endsection