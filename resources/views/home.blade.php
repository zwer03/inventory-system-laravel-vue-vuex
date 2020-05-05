@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <vue-page-transition name="fade">
                <router-view></router-view>
            </vue-page-transition>
        </div>
    </div>
</div>
@endsection
