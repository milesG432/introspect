@extends('layouts.app')
@section('content')
@if( Session::has('error'))
<div class="alert alert-danger" style="text-align: center">            
    <h3>{{Session::get('error')}}</h3>

</div>
@endif
@if(Session::has('message'))
<div class="alert-success" style="text-align: center">
    <h3>{{Session::get('message')}}</h3>
</div>
@endif
<nav id='adminPills'>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Staff</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Database admin</a>
        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">More stuff</a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        <h3>
            Staff:
        </h3>  
        <button type="button" class="bg-success" data-toggle="modal" data-target="#basicExampleModal" id='newUserBtn'>
            New staff member
        </button> 

        @if(isset($staff))
        <table id="myTable" class='table table-hover'>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Username</th>
                    <th>Access Level</th>
                    <th>Delete Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff as $user)
                <tr>
                    <td>{{$user->firstName}} {{$user->lastName}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->username}}</td>
                    <td>{{$user->level}}</td>
                    <td><a class="btn-outline-warning" href='#'>Delete User</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <!--TODO NO USERS WARNING-->
        @endif
    </div>
    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
        <h3>
            Database Administration and maintenance
        </h3>        
    </div>
    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
        <h3>
            Still TODO
        </h3>
    </div>
</div>
@endsection