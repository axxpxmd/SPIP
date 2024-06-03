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
                        Menampilkan Data Quesioner {{ $nama_instansi }} ( {{ $tahun }} )
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="#"><i class="icon icon-arrow_back"></i>Semua Data</a>
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
                        <div class="card">
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
                                        <label class="col-sm-2 fs-13"><strong>Nama Operator</strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $nOperator }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Jabatan Operator</strong></label>
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
                        @if ($countResultVerif == $countQuesioners)
                        <div class="alert alert-success text-center my-2 font-weight-bold" role="alert">
                            <span class="fs-14">Kuesioner ini telah selesai diverifikasi</span>
                        </div>
                        @else
                        <div class="alert alert-danger text-center my-2 font-weight-bold" role="alert">
                            <span class="fs-14">Terdapat {{ $countQuesioners - $countResultVerif }} kuesioner yang belum diverifikasi!</span>
                        </div>
                        @endif
                        @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show bdr-5 col-md-12 container mt-1" id="successAlert" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <div class="card">
                            <h6 class="card-header font-weight-bold text-black">Hasil Pengisian Kuesioner</h6>
                            <div class="card-body">
                                <div class="col-md-12 text-black">
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Status Pengisian </strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $countResult }} dari {{ $countQuesioners }} Pertanyaan | {{ $getPercent }}%</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Status Verifikasi </strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $countResultVerif }} dari {{ $countResult }} Pertanyaan | {{ $getPercentVerif }}%</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Total Kuesioner Direvisi </strong></label>
                                        <label class="col-sm-10 fs-13">: {{ $totalRevisi }} Pertanyaan</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @foreach ($indikators as $index => $i)
                        <div class="card mt-2">
                            <div class="card-body">
                                <div class="col-md-12">
                                    @php
                                        $nilaiIndikator = App\TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                                                ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
                                                ->where('tm_quesioners.indikator_id', $i->id)
                                                ->where('tm_results.user_id', $userId)
                                                ->where('tm_quesioners.tahun_id', $tahunId)
                                                ->orderBy('tm_results.status', 'ASC')
                                                ->first();
                                    @endphp
                                    <p class="font-weight-bold text-black"> {{ $index+1 }}. {{ $i->n_indikator }}</p>
                                    <div class="ml-2 mb-2" style="margin-top: -15px !important">
                                        <span>{{ $i->deskripsi }}</span>
                                    </div>
                                    <ol>
                                        @php
                                             $datas = App\TmResult::select('tm_results.id as id','status_revisi','status_kirim', 'tm_questions.n_question', 'tm_questions.id as id_question', 'tm_quesioners.id as id_quesioner', 'status', 'tm_results.answer_id as answer_id', 'keterangan_revisi', 'answer_id_revisi', 'keterangan')
                                                        ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                                                        ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
                                                        ->where('tm_quesioners.indikator_id', $i->id)
                                                        ->where('tm_results.user_id', $userId)
                                                        ->where('tm_quesioners.tahun_id', $tahunId)
                                                        ->orderBy('tm_quesioners.id', 'ASC')
                                                        ->get();
                                        @endphp
                                        @foreach ($datas as $indexq => $q)
                                            @php
                                                $answers = App\Models\TrQuesionerAnswer::where('quesioner_id', $q->id_quesioner)->get();
                                            @endphp
                                            @php
                                                $files = App\Models\TrResultFile::where('result_id', $q->id)->get();
                                            @endphp
                                            <div id="pertanyaanDiv{{ $index }}{{ $indexq }}">
                                                <li type="disc" class="text-black font-weight-normal mt-2">{{ $q->n_question }}
                                                    @if ($q->status_revisi == 1)
                                                        <span class="text-danger font-weight-bold">( Sedang Direvisi )</span>
                                                    @endif
                                                    @if ($q->status == 1)
                                                        <i title="sudah terverifikasi" class="icon icon-verified_user ml-1 text-primary"></i>
                                                    @endif
                                                </li>
                                            </div>
                                            @foreach ($answers as $index2 => $a)
                                            <div class="form-check mt-1">
                                                <input type="radio" class="form-check-input" value="{{ $a->answer->id }}" {{ $a->answer->id == $q->answer_id_revisi ? "checked" : "disabled" }} >
                                                <input type="radio" class="form-check-input" value="{{ $a->answer->id }}" {{ $a->answer->id == $q->answer_id ? "checked" : "disabled" }} >
                                                @if ($q->answer_id == $q->answer_id_revisi)
                                                <label class="form-check-label fs-14 {{ $a->answer->id == $q->answer_id ? "text-primary" : "-" }} font-weight-normal">{{ $a->answer->jawaban }}</label>
                                                @else
                                                <label class="form-check-label fs-14 {{ $a->answer->id == $q->answer_id_revisi ? "text-danger" : "-" }} {{ $a->answer->id == $q->answer_id ? "text-primary" : "-" }} font-weight-normal">{{ $a->answer->jawaban }}</label>
                                                @endif
                                            </div>
                                            @endforeach
                                            <div class="mt-1">
                                                <span class=" text-black"><strong class="text-black">Keterangan :</strong> {{ $q->keterangan }} </span>
                                            </div>
                                            <div class="mt-1">
                                                <span class=""><strong class="text-black">File :</strong></span>
                                                @forelse ($files as $f)
                                                    (<a target="blank" href="{{ config('app.sftp_src').$path.$f->file }}"> {{ $f->file }} </a>)
                                                @empty
                                                    <span>tidak ada file</span>
                                                @endforelse
                                            </div>
                                            <div class="mb-4">
                                                <div class="mt-1 mb-2">
                                                    <span class="text-danger"><strong class="text-black">Penjelasan Verifikator :</strong> {{ $q->keterangan_revisi ? $q->keterangan_revisi : '-' }}</span>
                                                </div>
                                                @php
                                                    $element = $index.$indexq;
                                                @endphp
                                                @if ($role_id != 8)
                                                    @if ($q->status_revisi == 0 && $q->status == 0)
                                                        <button class="btn btn-success btn-sm" data-toggle="modal" onclick="getRouteForm({{ $q->id }}, {{ $element }})" data-target="#verifikasi"><i class="icon-check mr-2"></i>Verifikasi</button>
                                                        <a class="btn btn-primary btn-sm" href="{{ route('verifikasi.edit', $q->id . '?element=pertanyaanDiv'.$index.$indexq) }}"><i class="icon-edit mr-2"></i>Edit Jawaban</a>
                                                        @if (!$q->answer_id_revisi)
                                                        <button class="btn btn-danger btn-sm" data-toggle="modal" onclick="getRevisiForm({{ $q->id }}, {{ $element }})" data-target="#revisi"><i class="icon-check mr-2"></i>Revisi</button>
                                                        @endif
                                                    @endif
                                                    @if ($q->status == 1)
                                                    <a href="{{ route('verifikasi.batalkanVerifikasi', $q->id . '?element=pertanyaanDiv'.$index.$indexq) }}" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin membatalkan verifikasi data ini?')"><i class="icon icon-times"></i> Batalkan Verifikasi</a>
                                                    @endif
                                                @endif
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
                        Apakah sudah yakin untuk memverifikasi quesioner ini ?
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer border-0 p-2">
                    <form id="routeForm" method="GET">
                        {{ csrf_field() }}
                        {{ method_field('GET') }}
                        <input type="hidden" name="element" id="element-div" value="{{ $element }}">
                        <button class="btn btn-sm btn-success"><i class="icon-check mr-2"></i>Verifikasi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="revisi" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="revisiForm" method="GET">
                    {{ csrf_field() }}
                    {{ method_field('GET') }}
                    <input type="hidden" name="element" id="element-div1" value="{{ $element }}">
                    <div class="modal-header">
                        <h6 class="modal-title font-weight-normal text-black fs-14" id="exampleModalLabel">
                            Apakah anda yakin untuk merevisi quesioner ini ?
                        </h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row form-inline">
                            <div class="col-md-12">
                                <!-- user -->
                                <div class="form-group m-0">
                                    <label for="pesan" class="text-right s-12 col-md-2">Penjelasan<span class="text-danger ml-1">*</span></label>
                                    <textarea type="text" name="pesan" id="pesan" class="form-control r-0 light s-12 col-md-10" rows="3" autocomplete="off" required placeholder="Berikan Penjelasan Revisi"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-2">
                        <button class="btn btn-sm btn-success"><i class="icon-check mr-2"></i>Kirim Revisi</button>
                    </div>
                </form>
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

    function getRouteForm(id, element){
        var length = element.toString().length;
        if (length == 1) {
            var d = `0${element}`
        } else {
            var d = element
        }

        $('#element-div').val(d);

        $('#routeForm').attr('action', "{{ route('verifikasi.confirm', ':id') }}".replace(':id', id));
    }

    function getRevisiForm(id, element){
        var length = element.toString().length;
        if (length == 1) {
            var d = `0${element}`
        } else {
            var d = element
        }

        $('#element-div1').val(d);

        $('#revisiForm').attr('action', "{{ route('verifikasi.updateRevisi', ':id') }}".replace(':id', id));
    }

    $(document).ready(function() {
        $("#errorAlert").fadeTo(5000, 1000).slideUp(1000, function() {
            $("#errorAlert").slideUp(1000);
        });
    });
</script>
@endsection
