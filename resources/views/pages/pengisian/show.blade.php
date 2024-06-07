@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-document-text6"></i>
                        Menampilkan Data Quesioner Tahun {{ $time->tahun }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
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
                        <div class="card mt-2">
                            <h6 class="card-header font-weight-bold text-black">Detail Instansi</h6>
                            <div class="card-body">
                                <div class="col-md-12 text-black">
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Nama Kepala</strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $nKepala }}</label>
                                    </div>
                                    <div class="row mt-n1">
                                        <label class="col-sm-2 fs-13"><strong>Jabatan Kepala</strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $jKepala }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Nama Asesor</strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $nOperator }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Jabatan Asesor</strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $jOperator }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Email</strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $email }}</label>
                                    </div>
                                    <div class="row mt-n1">
                                        <label class="col-sm-2 fs-13"><strong>No Telp</strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $noTelp }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-2">
                            <h6 class="card-header font-weight-bold text-black">Hasil Pengisian Kuesioner</h6>
                            <div class="card-body">
                                <div class="col-md-12 text-black">
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Status Pengisian </strong></label>
                                        @if ($countResult == $countQuesioners)
                                        <label class="col-sm-10 fs-13">: {{ $countResult }} dari {{ $countQuesioners }} Pertanyaan | {{ $getPercent }}% </label>
                                        @else
                                        <label class="col-sm-10 fs-13">: {{ $countResult }} dari {{ $countQuesioners }} Pertanyaan | {{ $getPercent }}% | <a href="{{ route('form-quesioner.create', array('tahun_id' => $tahunId)) }}">Lanjutkan Mengerjakan<i class="icon-arrow_forward"></i></a></label>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Kuesioner Terkirim </strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $countResultTerkirim }} Pertanyaan</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Status Verifikasi </strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $countResultVerif }} dari {{ $countResult }} Pertanyaan | {{ $getPercentVerif }}%</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Status Kuesioner</strong></label>
                                        <label class="col-sm-10 fs-13">:
                                            @if ($status_kirim != 0)
                                                Belum Terkirim
                                            @else
                                                @if ($countResultVerif == $countQuesioners)
                                                    Sudah Diverifikasi <i title="SUDAH TERVERIFIKASI" class="icon icon-verified_user ml-1 text-primary"></i>
                                                @else
                                                    Sedang Diverifikasi
                                                @endif
                                            @endif
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="mt-2">
                                        @if ($result == true)
                                            @if ($status_kirim > 0)
                                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#verifikasi"><i class="icon-send mr-2"></i>Kirim untuk diverifikasi</button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @foreach ($indikators as $index => $i)
                        <div class="card mt-2">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <!-- Indikator -->
                                    <p class="font-weight-bold text-black">{{ $i->n_indikator }}</p>
                                    <div class="ml-2 mb-2" style="margin-top: -15px !important">
                                        <span>{{ $i->deskripsi }}</span>
                                    </div>
                                    <ol>
                                        @php
                                             $datas = App\TmResult::select('tm_results.id as id','status_kirim','status_revisi', 'tm_questions.n_question', 'tm_questions.id as id_question', 'tm_quesioners.id as id_quesioner', 'status', 'tm_results.answer_id as answer_id', 'keterangan_revisi', 'keterangan', 'status_kirim', 'answer_id_revisi')
                                                        ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                                                        ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
                                                        ->where('tm_quesioners.indikator_id', $i->id)
                                                        ->where('tm_results.user_id', $userId)
                                                        ->where('tm_quesioners.tahun_id', $tahunId)
                                                        ->get();
                                        @endphp
                                        @foreach ($datas as $q)
                                            @php
                                                $answers = App\Models\TrQuesionerAnswer::where('quesioner_id', $q->id_quesioner)->get();
                                            @endphp
                                            @php
                                                $files = App\Models\TrResultFile::where('result_id', $q->id)->get();
                                            @endphp
                                            <!-- Question -->
                                            <li type="disc" class="text-black font-weight-normal mt-2">
                                                {{ $q->n_question }}
                                                @if ($q->status == 1)
                                                    <i title="SUDAH TERVERIFIKASI" class="icon icon-verified_user ml-1 text-primary"></i>
                                                @endif
                                            </li>
                                            <!-- Answer -->
                                            @foreach ($answers as $index2 => $a)
                                            <div class="form-check mt-1">
                                                <input type="radio" class="form-check-input" value="{{ $a->answer->id }}" {{ $a->answer->id == $q->answer_id_revisi ? "checked" : "disabled" }} >
                                                <input type="radio" class="form-check-input" value="{{ $a->answer->id }}" {{ $a->answer->id == $q->answer_id ? "checked" : "disabled" }} >
                                                <label class="form-check-label fs-14 {{ $a->answer->id == $q->answer_id_revisi ? "text-danger" : "-" }} {{ $a->answer->id == $q->answer_id ? "text-primary" : "-" }} font-weight-normal">{{ $a->answer->jawaban }}</label>
                                            </div>
                                            @endforeach
                                            <div class="mt-1">
                                                <span class=" text-black"><strong class="text-black">Keterangan :</strong> {{ $q->keterangan }} </span>
                                            </div>
                                            <div class="mt-1">
                                                <span class=""><strong class="text-black">File :</strong></span>
                                                @forelse ($files as $f)
                                                    (<a target="_blank" href="{{ config('app.sftp_src').$path.$f->file }}"> {{ $f->file }} </a>)
                                                @empty
                                                    <span>tidak ada file</span>
                                                @endforelse
                                            </div>
                                            <div class="mt-1 mb-4">
                                                @if ($q->status_kirim == 1)
                                                <div class="mt-1 mb-2">
                                                    <span class="text-danger"><strong class="text-black">Penjelasan Verifikator :</strong> {{ $q->keterangan_revisi ? $q->keterangan_revisi : '-' }}</span>
                                                </div>
                                                @endif
                                                <div>
                                                    @if ($q->status_kirim != 1)
                                                    <a class="btn btn-primary btn-sm mt-2 mb-2" href="{{ route('hasil.edit', $q->id) }}"><i class="icon-edit mr-2"></i>Edit Data</a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="verifikasi" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title font-weight-normal text-black fs-14" id="exampleModalLabel">
                        Apakah sudah yakin untuk mengirim hasil kuesioner untuk diverifikasi ?
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer border-0 p-2">
                    <form action="{{ route('hasil.sendQuesioner') }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('POST') }}
                        <input type="hidden" name="user_id" value="{{ $userId }}">
                        <input type="hidden" name="tahun_id" value="{{ $tahunId }}">
                        <button class="btn btn-sm btn-primary"><i class="icon-send mr-2"></i>Kirim</button>
                    </form>
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
