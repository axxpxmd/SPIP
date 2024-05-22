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
                                <p class="mb-0 font-weight-normal fs-18 text-black">SELAMAT DATANG DI APLIKASI SAKIP</p>
                                <p class="mt-0 font-weight-normal text-black">Anda Login Sebagai Role ( {{ Auth::user()->modelHasRole->role->name }} )</p>
                            </div>
                        </div>
                    </div>
                    @if ($role_id == 1)
                    <div class="col-md-12 mt-3">
                        <div class="row">
                            <div class="col-md-3 px-1 mb-5-m">
                                <div class="card no-b r-15">
                                    <h6 class="card-header font-weight-bold text-white" style="background: #7DC855; border-top-right-radius: 15px; border-top-left-radius: 15px">Total Perangkat Daerah</h6>
                                    <div class="card-body text-center">
                                        <div class="mb-2">
                                            <i class="icon-notebook-text fs-24 text-success mr-2"></i>
                                            <span class="m-0 font-weight-bold fs-16">{{ $totalPerangkatDaerah }}</span>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 px-1 mb-5-m">
                                <div class="card no-b r-15">
                                    <h6 class="card-header font-weight-bold text-white" style="background: #4285F4; border-top-right-radius: 15px; border-top-left-radius: 15px">Total OPD Mengisi Kuesioner</h6>
                                    <div class="card-body text-center">
                                        <div class="mb-2">
                                            <i class="icon-notebook-text fs-24 text-primary mr-2"></i>
                                            <span class="m-0 font-weight-bold fs-16">{{ $totalPengisianKuesioner }} ( {{ round($totalPengisianKuesioner / $totalPerangkatDaerah * 100, 2) }}% )</span>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 px-1 mb-5-m">
                                <div class="card no-b r-15">
                                    <h6 class="card-header font-weight-bold text-white" style="background: #ED5564; border-top-right-radius: 15px; border-top-left-radius: 15px">Total OPD Mengirim Kuesioner</h6>
                                    <div class="card-body text-center">
                                        <div class="mb-2">
                                            <i class="icon-notebook-text fs-24 text-danger mr-2"></i>
                                            <span class="m-0 font-weight-bold fs-16">{{ count($totalKuesionerTerkirim) }}</span>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 px-1 mb-5-m">
                                <div class="card no-b r-15">
                                    <h6 class="card-header font-weight-bold text-white" style="background: #FDC90F; border-top-right-radius: 15px; border-top-left-radius: 15px">Total Kuesioner</h6>
                                    <div class="card-body text-center">
                                        <div class="mb-2">
                                            <i class="icon-notebook-text fs-24 amber-text mr-2"></i>
                                            <span class="m-0 font-weight-bold fs-16">{{ $totalKuesioner }}</span>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div class="row">
                            <div class="col-md-4  px-1 mb-5-m">
                                <div class="card no-b r-15">
                                    <h6 class="card-header text-white font-weight-bold bg-blue-grey" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Daftar OPD Yang Sedang Revisi</h6>
                                    <div class="card-body">
                                        <table class="table table-hover fs-12" cellspacing="0" width="100%">
                                            <thead>
                                                <tr class="font-weight-bold">
                                                    <th>#</th>
                                                    <th>Perangkat Daerah</th>
                                                    <th>Total Revisi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($listOpdRevisi as $key => $i)
                                                @php
                                                    $total_revisi = App\TmResult::whereNotNull('message')
                                                                    ->where('status_kirim', 0)
                                                                    ->where('tm_results.user_id', $i->user_id)
                                                                    ->count();
                                                @endphp
                                                    <tr>
                                                        <td>{{ $key+1 }}</td>
                                                        <td>{{ $i->user->pegawai->nama_instansi }}</td>
                                                        <td>{{ $total_revisi }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
