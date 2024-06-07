@extends('layouts.app')
@section('title', '| Dashboard  ')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-home"></i>
                        Home Page
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content pb-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="v-pills-1">
                <div class="mt-3">
                    <div class="card mt-2 r-15 no-b">
                        <div class="card-body">
                            <div class="text-center">
                                <p class="mb-0 font-weight-normal fs-18 text-black">SELAMAT DATANG DI APLIKASI SPIP</p>
                                <p class="mt-0 font-weight-normal text-black">Anda Login Sebagai Role ( {{ Auth::user()->modelHasRole->role->name }} )</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
