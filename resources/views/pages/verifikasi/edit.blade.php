@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-document-text6 mr-2"></i>
                        Revisi Quesioner {{ $nama_instansi }} ( {{ $tahun }} )
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route('verifikasi.show', array('tahun_id' => $tahunId, 'user_id' => $userId, '#' . $element)) }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content my-3" id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show bdr-5 col-md-12 container mt-1" id="successAlert" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <form action="{{ route('verifikasi.sendRevisi', $data->id) }}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="element" value="{{ $element }}">
                            <div class="card mt-2">
                                <div class="card-body">
                                    <p class="font-weight-bold text-black"> - {{ $data->quesioner->indikator->n_indikator }}</p>
                                    <div class="ml-2 mb-2" style="margin-top: -15px !important">
                                        <span>{{ $data->quesioner->indikator->deskripsi }}</span>
                                    </div>
                                    <div class="ml-5">
                                        <span class="text-black font-weight-normal mt-2">1. {{ $data->quesioner->question->n_question }}</span>
                                        @foreach ($answers as $index2 => $a)
                                        <div class="form-check ml-3">
                                            <input type="radio" class="form-check-input" name="answer_id_revisi" id="answer_id_revisi" value="{{ $a->answer->id }}" {{ $a->answer->id == $data->answer->id ? "checked" : "-" }}>
                                            <label class="form-check-label {{ $a->answer->id == $data->answer_id_revisi ? 'text-danger' : '-' }} fs-14">{{ $a->answer->jawaban }}  {{ $a->answer->id == $data->answer_id_revisi ? "( Jawaban Verifikator )" : "" }}</label>
                                        </div>
                                        @endforeach
                                        <div class="ml-3">
                                            <div class="mt-2">
                                                <span><strong class="font-weight-bold">Keterangan :</strong> {{ $data->keterangan }} </span>
                                            </div>
                                            <div class="mt-2">
                                                <span class="font-weight-bold">File :</span>
                                                @forelse ($files as $f)
                                                    (<a target="blank" href="{{ config('app.sftp_src').$path.$f->file }}"> {{ $f->file }} </a>)
                                                @empty
                                                    <span>-</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-2">
                                <div class="card-body">
                                    <div class="form-row form-inline">
                                        <div class="col-md-8">
                                            <div class="form-group m-0">
                                                <label for="pesan" class="col-form-label s-12 col-md-2">Penjelasan Verifikator</label>
                                                <textarea name="pesan" rows="3" id="pesan" class="form-control r-0 light s-12 col-md-6" autocomplete="off" required>{{ $data->keterangan_revisi }}</textarea>
                                            </div>
                                            <div class="form-group mt-2">
                                                <div class="col-md-2"></div>
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="icon-check mr-2"></i>Simpan <span id="txtAction"></span></button>
                                            </div>
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
@endsection
@section('script')
<script type="text/javascript">


    $(document).ready(function() {
        $("#successAlert").fadeTo(5000, 1000).slideUp(1000, function() {
            $("#successAlert").slideUp(1000);
        });
    });

    $(document).ready(function() {
        $("#errorAlert").fadeTo(5000, 1000).slideUp(1000, function() {
            $("#errorAlert").slideUp(1000);
        });
    });
</script>
@endsection
