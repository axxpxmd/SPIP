@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-building mr-1"></i>
                        {{ $title }}
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid my-3 relative animatedParent animateOnce">
        <div class="tab-content " id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="card">
                            <h6 class="card-header"><strong>Input Data Tahun 2022</strong></h6>
                            <div class="card-body">
                                <p class="text-black font-weight-bold p-2" style="background-color: #F5F8FA">{{ $user->pegawai->nama_instansi }}</p>
                                <form action="{{ route('verifikasi.cetakRekapLke') }}" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="tahun_id" value="{{ $tahun_id }}">
                                    <input type="hidden" name="user_id" value="{{ $user_id }}">
                                    <div class="row">
                                        <div class="col-md-8">
                                            @foreach ($indikator as $i)
                                            @php
                                                $data = App\Models\Indikator2023::where('tahun_id', $tahun_id)->where('user_id', $user_id)->where('n_indikator', $i->indikator_t)->first();
                                            @endphp
                                            <input type="hidden" name="n_indikator[]" value="{{ $i->indikator_t }}">
                                            <div class="row mb-1">
                                                <label for="nilai" class="col-form-label s-12 col-sm-4 text-right font-weight-bold">{{ $i->indikator_t }}<span class="text-danger ml-1">*</span></label>
                                                <div class="col-sm-3">
                                                    <input type="text" name="nilai[]" id="nilai" value="{{ $data ? $data->nilai : null }}" class="form-control r-0 light s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            @endforeach
                                            <div class="row mb-2">
                                                <label class="col-sm-4"></label>
                                                <div class="col-md-3">
                                                    <button type="submit" id="action" class="btn btn-block btn-success btn-sm"><i class="icon icon-print"></i>Cetak Rekap LKE</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    //
</script>
@endsection
