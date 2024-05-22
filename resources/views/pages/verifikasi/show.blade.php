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
                        <a class="nav-link" href="{{ route($route.$routeBack) }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
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
                                        <label class="col-sm-10 fs-13">: {{ $countResultVerif }} dari {{ $countQuesioners }} Pertanyaan | {{ $getPercentVerif }}%</label>
                                    </div>
                                    <hr>
                                    @if ($countResultVerif == $countQuesioners)
                                    <div class="row mb-2">
                                        <label class="col-sm-2 fs-13"><strong>File LHE Verifikator</strong></label>
                                        <label class="col-sm-10 fs-13">
                                            @if ($file_lhe)
                                                <a href="{{ config('app.sftp_src').$path.$file_lhe->file }}" target="_blank" class="mr-4"><i class="icon icon-link2 mr-2"></i>Lihat File</a>
                                                @if ($role_id != 8)
                                                <button class="font-weight-bold btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#uploadLHE"><i class="icon icon-file_upload"></i>Edit LHE</button>
                                                @endif
                                            @else
                                                @if ($role_id != 8)
                                                <button class="font-weight-bold btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#uploadLHE"><i class="icon icon-file_upload"></i>Upload LHE</button>
                                                @endif
                                            @endif
                                        </label>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2 fs-13"><strong>Tindak Lanjut File LHE</strong></label>
                                        <label class="col-sm-10 fs-13">
                                            @if ($file_lhe)
                                                @if ($file_lhe_tindak_lanjut)
                                                    <a href="{{ config('app.sftp_src').$path.$file_lhe_tindak_lanjut->file }}" target="_blank" class="btn btn-sm btn-secondary mr-2"><i class="icon icon-link"></i>Download File</a>
                                                @else
                                                <i>belum ditindak lanjut</i>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="row mt-2">
                                        <label class="col-sm-2 fs-13"></label>
                                        <label class="col-sm-10 fs-13">
                                            <a href="{{ route('verifikasi.cetakReport', array('tahun_id' => $tahunId, 'user_id' => $userId)) }}" target="_blank" class="btn btn-sm btn-primary mr-2"><i class="icon icon-print"></i>Cetak LKE</a>
                                            <a href="{{ route('verifikasi.inputDataTahunSebelum', array('tahun_id' => $tahunId, 'user_id' => $userId)) }}" target="_blank" class="btn btn-sm btn-success mr-2"><i class="icon icon-print"></i>Cetak Rekap LKE</a>
                                        </label>
                                    </div>
                                    @endif
                                    <p class="text-black font-weight-bold p-2" style="background-color: #F5F8FA">Total Nilai Per Indikator</p>
                                    @php
                                        $nilaiAkuntabilitas = 0;
                                    @endphp
                                    @foreach ($getIndikator as $indexg => $g)
                                        @php
                                            $getIdIndikator = App\TmResult::select('tm_quesioners.indikator_id as indikator_id')
                                                ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                                                ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
                                                ->where('tm_quesioners.indikator_t', $g->indikator_t)
                                                ->where('tm_results.user_id', $userId)
                                                ->where('tm_quesioners.tahun_id', $tahunId)
                                                ->groupBy('tm_quesioners.indikator_id')
                                                ->get();
                                                $kk = 0;
                                                $detailNilai = [];
                                        @endphp
                                        @foreach ($getIdIndikator as $keyIn => $in)
                                            @php
                                                $nilaiIndikator1 = App\TmResult::select(DB::raw("sum(nilai_awal) as nilai_awal"), DB::raw("sum(nilai_akhir) as nilai_akhir"), 'tm_quesioners.bobot_sub_indikator as bobot_sub_indikator', 'pengali_bobot')
                                                    ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                                                    ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
                                                    ->where('tm_quesioners.indikator_id', $in->indikator_id)
                                                    ->where('tm_results.user_id', $userId)
                                                    ->where('tm_quesioners.tahun_id', $tahunId)
                                                    ->orderBy('tm_results.status', 'ASC')
                                                    ->first();
                                                $nilaiAkuntabilitas += App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator1->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $nilaiIndikator1->bobot_sub_indikator, $nilaiIndikator1->pengali_bobot );
                                                if ($g->indikator_t == 'Perencanaan Kinerja') {
                                                    $kk += App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator1->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $nilaiIndikator1->bobot_sub_indikator, $nilaiIndikator1->pengali_bobot );
                                                    $detailNilai[$keyIn] = App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator1->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $nilaiIndikator1->bobot_sub_indikator, $nilaiIndikator1->pengali_bobot );
                                                }elseif($g->indikator_t == 'Pengukuran Kinerja'){
                                                    $kk += App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator1->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $nilaiIndikator1->bobot_sub_indikator, $nilaiIndikator1->pengali_bobot );
                                                    $detailNilai[$keyIn] = App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator1->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $nilaiIndikator1->bobot_sub_indikator, $nilaiIndikator1->pengali_bobot );
                                                }elseif($g->indikator_t == 'Pelaporan Kinerja'){
                                                    $kk += App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator1->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $nilaiIndikator1->bobot_sub_indikator, $nilaiIndikator1->pengali_bobot );
                                                    $detailNilai[$keyIn] = App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator1->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $nilaiIndikator1->bobot_sub_indikator, $nilaiIndikator1->pengali_bobot );
                                                }elseif($g->indikator_t == 'Evaluasi Akuntabilitas Kinerja Internal'){
                                                    $kk += App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator1->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $nilaiIndikator1->bobot_sub_indikator, $nilaiIndikator1->pengali_bobot );
                                                    $detailNilai[$keyIn] = App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator1->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $nilaiIndikator1->bobot_sub_indikator, $nilaiIndikator1->pengali_bobot );
                                                }
                                           @endphp
                                        @endforeach
                                        <p class="text-black m-0"> {{ $indexg+1 }}. {{ $g->indikator_t }}</p>
                                        <div class="col-md-12 text-black">
                                            <div class="row">
                                                <label class="col-sm-2 fs-13">Bobot </label>
                                                <label class="col-sm-10 fs-13 font-weight-bold">: {{ $g->bobot_indikator }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-2 fs-13">Nilai </label>
                                                <label class="col-sm-10 fs-13">:
                                                    <span class="font-weight-bold">{{ $kk }}</span>
                                                    @if (count($detailNilai))
                                                        <span class="ml-5">Detail Nilai</span> :
                                                        @foreach ($detailNilai as $z)
                                                            <span>( {{ $z }} )</span>
                                                        @endforeach
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                    <hr>
                                    <div class="col-md-12 text-black">
                                        <div class="row">
                                            <label class="col-sm-2 fs-13 font-weight-bold">NILAI AKUNTABILITAS KINERJA </label>
                                            <label class="col-sm-10 fs-13 font-weight-bold">: {{ $nilaiAkuntabilitas }} ( {{ App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBladeRekap($nilaiAkuntabilitas) }} ) </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @foreach ($indikators as $index => $i)
                        <div class="card mt-2">
                            <div class="card-body">
                                <div class="col-md-12">
                                    @php
                                        $nilaiIndikator = App\TmResult::select(DB::raw("sum(nilai_awal) as nilai_awal"), DB::raw("sum(nilai_akhir) as nilai_akhir"), 'tm_quesioners.bobot_sub_indikator as bobot_sub_indikator', 'pengali_bobot')
                                                ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                                                ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
                                                ->where('tm_quesioners.indikator_id', $i->id)
                                                ->where('tm_results.user_id', $userId)
                                                ->where('tm_quesioners.tahun_id', $tahunId)
                                                ->orderBy('tm_results.status', 'ASC')
                                                ->first();
                                    @endphp
                                    <div class="col-md-12 text-black">
                                        <div class="row">
                                            <label class="col-sm-2 fs-13"><strong>Nilai Awal</strong></label>
                                            <label class="col-sm-10 fs-13">: {{ round($nilaiIndikator->nilai_awal, PHP_ROUND_HALF_UP, 2) }} ( {{ App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator->nilai_awal, PHP_ROUND_HALF_UP, 2)) }} )</label>
                                        </div>
                                        <div class="row mt-n1">
                                            <label class="col-sm-2 fs-13"><strong>Nilai Akhir </strong></label>
                                            <label class="col-sm-10 fs-13">:
                                                {{ $nilaiIndikator->nilai_akhir }}
                                                @php
                                                    $nilaiHuruf = App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade($nilaiIndikator->nilai_akhir);
                                                @endphp
                                                ( {{ $nilaiHuruf }} )
                                                ( {{ App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkPengaliBobot($nilaiHuruf, $nilaiIndikator->pengali_bobot) }} )
                                            </label>
                                        </div>
                                        <div class="row mt-n1">
                                            <label class="col-sm-2 fs-13"><strong>Nilai Bobot</strong></label>
                                            <label class="col-sm-10 fs-13">
                                                : {{ $nilaiIndikator->bobot_sub_indikator }}
                                            </label>
                                        </div>
                                        <div class="row mt-n1">
                                            <label class="col-sm-2 fs-13"><strong>Total Bobot Final</strong></label>
                                            <label class="col-sm-10 fs-13">
                                                : {{ App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($nilaiIndikator->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $nilaiIndikator->bobot_sub_indikator, $nilaiIndikator->pengali_bobot ) }}
                                            </label>
                                        </div>
                                    </div>
                                    <p class="font-weight-bold text-black"> {{ $index+1 }}. {{ $i->n_indikator }}</p>
                                    <div class="ml-2 mb-2" style="margin-top: -15px !important">
                                        <span>{{ $i->deskripsi }}</span>
                                    </div>
                                    <ol>
                                        @php
                                             $datas = App\TmResult::select('tm_results.id as id','status_revisi','status_kirim', 'tm_questions.n_question', 'tm_questions.id as id_question', 'tm_quesioners.id as id_quesioner', 'nilai_akhir','nilai_awal', 'status', 'tm_results.answer_id as answer_id', 'message', 'answer_id_revisi', 'keterangan')
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
                                                    @if ($q->status == 1)
                                                        <i title="sudah terverifikasi" class="icon icon-verified_user ml-1 text-primary"></i> <span class="font-weight-bold">({{ $q->nilai_akhir }})</span>
                                                    @endif
                                                    @if ($q->status_kirim == 0 && $q->message != null)
                                                    <span class="text-danger font-weight-bold">( Sedang Direvisi )</span>
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
                                                <div class="mt-1">
                                                    <span class="text-danger"><strong class="text-black">Penjelasan :</strong> {{ $q->message }}</span>
                                                </div>
                                                <div class="mt-1">
                                                    <span class=""><strong class="text-black">Nilai Awal :</strong> {{ round($q->nilai_awal, 2) }}</span>
                                                </div>
                                                <div class="mt-1 mb-2">
                                                    <span class=""><strong class="text-black">Nilai Akhir :</strong> {{ $q->nilai_akhir }}</span>
                                                </div>
                                                @php
                                                    $element = $index.$indexq;
                                                @endphp
                                                @if ($role_id != 8)
                                                    @if ($q->status != 1 && $q->status_kirim == 1)
                                                        <button class="btn btn-success btn-sm" data-toggle="modal" onclick="getRouteForm({{ $q->id }}, {{ $element }})" data-target="#verifikasi"><i class="icon-check mr-2"></i>Verifikasi</button>
                                                        {{-- @if ($q->status_revisi != 1) --}}
                                                            <a class="btn btn-primary btn-sm" href="{{ route('verifikasi.edit', $q->id . '?element=pertanyaanDiv'.$index.$indexq) }}"><i class="icon-edit mr-2"></i>Edit Nilai</a>
                                                        {{-- @endif --}}
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
    <div class="modal fade" id="uploadLHE" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('verifikasi.uploadLhe') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <input type="hidden" name="user_id" value="{{ $id }}">
                    <input type="hidden" name="tahun_id" value="{{ $tahunId }}">
                    <div class="modal-header">
                        <h6 class="modal-title font-weight-bold text-black fs-14" id="exampleModalLabel">
                            UPLOAD FILE LHE
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
                                    <label for="pesan" class="text-right s-12 col-md-3 font-weight-bold">File LHE<span class="text-danger ml-1">*</span></label>
                                    <input type="file" name="file" id="file" class="form-control r-0 light s-12 col-md-9" required>
                                </div>
                                <div class="form-group mt-1">
                                    <label class="col-md-3"></label>
                                    <button class="btn btn-sm btn-success"><i class="icon-save mr-2"></i>Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
