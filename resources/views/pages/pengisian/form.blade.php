<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <link rel="icon" href="{{ asset('images/template/tangsel.png') }}" type="image/x-icon">
    <title>SPIP | Form Quesioner</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/myStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets1/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets1/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets1/css/util.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    <style>
        .custom-file-input ~ .custom-file-label::after {
            content: "Pilih";
        }
    </style>

</head>
<body>
    <div class="container-contact100 justify-content-center">
        <div class="container p-0">
            <div class="col-md-12 p-0">
                <div class="wrap-contact100 p-0">
                    <div class="text-center p-2">
                        <p class="fs-21 font-weight-bold text-black">KUESIONER SISTEM PENGENDALIAN INTERN PEMERINTAH</p>
                        <p class="fs-21 font-weight-bold text-black text-uppercase" style="margin-top: -15px !important">DI LINGKUNGAN PERANGKAT DAERAH</p>
                        <p class="fs-21 font-weight-bold text-black text-uppercase" style="margin-top: -15px !important">KOTA TANGERANG SELATAN</p>
                        <p class="fs-21 font-weight-bold text-black text-uppercase" style="margin-top: -15px !important">TAHUN {{ $time->tahun }}</p>
                    </div>
                    <hr width="1100px" style="margin-top: -20px">
                    <div class="container">
                        <div class="col-md-12 text-black font-weight-bold">
                            <div class="row">
                                <label class="col-md-2"><strong>Nama Kepala</strong></label>
                                <label class="col-md-10 ml-n5 pl-0">: {{ $nKepala }}</label>
                            </div>
                            <div class="row mt-n1">
                                <label class="col-md-2"><strong>Jabatan Kepala</strong></label>
                                <label class="col-md-10 ml-n5 pl-0">: {{ $jKepala }}</label>
                            </div>
                            <div class="row">
                                <label class="col-md-2"><strong>Nama Asesor</strong></label>
                                <label class="col-md-10 ml-n5 pl-0">: {{ $nOperator }}</label>
                            </div>
                            <div class="row">
                                <label class="col-md-2"><strong>Jabatan Asesor</strong></label>
                                <label class="col-md-10 ml-n5 pl-0">: {{ $jOperator }}</label>
                            </div>
                            <div class="row">
                                <label class="col-md-2"><strong>Email</strong></label>
                                <label class="col-md-10 ml-n5 pl-0">: {{ $email }}</label>
                            </div>
                            <div class="row mt-n1">
                                <label class="col-md-2"><strong>No Telp</strong></label>
                                <label class="col-md-10 ml-n5 pl-0">: {{ $noTelp }}</label>
                            </div>
                            <div class="row mt-n1">
                                <label class="col-md-2"><strong>Status Pengisian</strong></label>
                                <label class="col-md-10 ml-n5 pl-0">: {{ $countResult }} dari {{ $countQuesioners }} Pertanyaan | {{ $getPercent }}%
                                    @if ($countResult >= 1)
                                    | <a class="fs-13" target="blank" href="{{ route('hasil.show', $time->id) }}">Lihat Hasil</a>
                                    @endif
                                </label>
                            </div>
                            <div class="mb-2">
                                <a class="fs-13" href="{{ route('home') }}"><i class="icon icon-arrow-left mr-2"></i>Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($countResult != $countQuesioners)
        <div class="container p-0 mt-1">
            <div class="col-md-12 p-0">
                <div class="wrap-contact100 p-0">
                    <div class="container text-black p-2">
                        @if ($countResult != 0)
                        <div class="alert alert-danger" role="alert">
                            <p class="m-0 text-danger font-weight-bold fs-14 text-center">TERDAPAT {{ $countQuesioners - $countResult }} KUESIONER YANG BELUM DIISI</p>
                        </div>
                        @endif
                        @include('layouts.alert')
                        <div class="ml-3 p-2 font-weight-bold">
                            <span>1. Tata cara Pengisian Lembar Kerja Evaluasi <a href="#" data-toggle="modal" data-target="#tataCaraPengisian">KLIK</a></span>
                            <br>
                            <span>2. Tata Cara Upload File <a href="#" data-toggle="modal" data-target="#tataCaraUpload">KLIK</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if ($countResult == $countQuesioners)
        <div class="container p-0 mt-1">
            <div class="col-md-12 p-0">
                <div class="wrap-contact100 p-0">
                    <div class="container text-black p-2">
                        <p class="text-center">Selamat! Quesioner sudah terisi semua.</p>
                        <p class="text-center mt-n3">Untuk melihat hasil pengisian quesioner bisa klik link berikut ini. <a href="{{ route('hasil.index') }}">Hasil Quesioner</a></p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if ($time->status == 1)
        <form action="{{ route('form-quesioner.store') }}" method="POST" id="form" enctype="multipart/form-data">
            {{ method_field('POST') }}
            {{ csrf_field() }}
            <input type="hidden" name="totalIndikator" id="totalIndikator" value="{{ $indikators->count() }}">
            <input type="hidden" name="tahun_id" value="{{ $time->id }}">
            <input type="hidden" name="page" value="{{ $page }}">
            @foreach ($indikators as $index => $i)
            <div class="container mt-2 p-0">
                <div id="alert"></div>
                <div class="col-md-12 p-0 mt-n1">
                    <div class="wrap-contact100 p-0">
                        <div class="p-3">
                            <!-- Indikator -->
                            <p class="text-black font-weight-bold mb-n1 ml-3">{{ $i->n_indikator }}</p>
                            <div class="m-l-35">
                                <span>{{ $i->deskripsi }}</span>
                            </div>
                            <!-- Question -->
                            @php
                                $total_questions = App\Models\Pertanyaan::join('tm_quesioners', 'tm_quesioners.question_id', '=', 'tm_questions.id')
                                    ->where('tm_quesioners.tahun_id', $tahunId)
                                    ->where('tm_questions.indikator_id', $i->id)
                                    ->get();
                                $questions = App\Models\Pertanyaan::join('tm_quesioners', 'tm_quesioners.question_id', '=', 'tm_questions.id')
                                    ->where('tm_quesioners.tahun_id', $tahunId)
                                    ->where('tm_questions.indikator_id', $i->id)
                                    ->whereIn('tm_questions.id', $checkQuestion)
                                    ->whereNotIn('tm_questions.id', $check)
                                    ->get();
                            @endphp
                            <input type="hidden" name="totalQuestion[]" value="{{ $questions->count() }}">
                            <input type="hidden" name="totalPertanyaan[]" value="{{ $total_questions->count() }}">
                            <ol  type="1">
                                @foreach ($questions as $indexes => $q)
                                <div class="ml-4 mt-2 mb-4">
                                    <div class="mb-1">
                                        <input type="hidden" name="quesioner_id{{ $index }}{{ $indexes }}" value="{{ $q->id }}">
                                        {{-- <span class="text-black font-weight-bold" style="margin-left: -10px !important">{{ $indexes+1 }}. {{ $q->n_question }}</span> --}}
                                        <li class="text-black font-weight-bold" style="margin-left: -10px !important">{{ $q->n_question }}</li>
                                    </div>
                                    <!-- Answer -->
                                    @php
                                        $answers = App\Models\TrQuesionerAnswer::where('quesioner_id', $q->id)->get();
                                    @endphp
                                    @foreach ($answers as $index2 => $a)
                                    <div class="form-check" style="margin-left: -12px !important">
                                        <input type="radio" class="form-check-input {{ $indexes+1 }}" id="answer{{ $index2 }}{{ $index }}{{ $indexes }}" name="answer_id{{ $index }}{{ $indexes }}" value="{{ $a->answer->id }}">
                                        <label class="form-check-label fs-14 text-black" for="answer{{ $index2 }}{{ $index }}{{ $indexes }}">{{ $a->answer->jawaban }}</label>
                                    </div>
                                    <script>
                                        updateList{{ $index }}{{ $indexes }} = function() {
                                            var input = document.getElementById('file'+{{ $index }}+{{ $indexes }});
                                            var output = document.getElementById('fileList'+{{ $index }}+{{ $indexes }});
                                            var children = "";
                                            for (var i = 0; i < input.files.length; ++i) {
                                                children += '<li type="1">'+ input.files.item(i).name + '</li>';
                                            }
                                            output.innerHTML = children;
                                            $('#fileTitle'+{{ $index }}+{{ $indexes }}).html(input.files.length + ' File dipilih');

                                            $('#answer'+{{ $index2 }}+{{ $index }}+{{ $indexes }}).prop('required', true);
                                        }

                                        $('#answer'+{{ $index2 }}+{{ $index }}+{{ $indexes }}).change(function () {
                                            $("#file"+{{ $index }}+{{ $indexes }}).prop('required', true);

                                            console.log('fkds')
                                            $('#pagination_display').css("display", "none");
                                        });
                                    </script>
                                    @endforeach
                                    <div style="margin-left: -10px">
                                        <div class="form-inline ml-3 mt-2">
                                            <textarea name="keterangan{{ $index }}{{ $indexes }}" cols="160" rows="3" class="form-control bg-light" placeholder="Keterangan ..." style="font-size: 14px !important"></textarea>
                                        </div>
                                        <div class="form-inline ml-3 mt-2 mb-2">
                                            <div class="custom-file col-md-12">
                                                <input type="file" class="custom-file-input" id="file{{ $index }}{{ $indexes }}" name="file{{ $index }}{{ $indexes }}[]" onchange="javascript:updateList{{ $index }}{{ $indexes }}()" multiple>
                                                <label class="custom-file-label" for="customFile" id="fileTitle{{ $index }}{{ $indexes }}">Pilih File</label>
                                            </div>
                                            <div class="col-md-6 mt-2 text-primary" id="fileList{{ $index }}{{ $indexes }}"></div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container p-0 mt-2">
                <div class="col-md-12 p-0">
                    <button type="submit" class="btn btn-light btn-sm font-weight-normal" id="submitButton"><i class="icon-save mr-2"></i>Simpan Kuesioner</button>
                    <div class="p-0 float-right" id="pagination_display">
                        {{ $indikators->links() }}
                    </div>
                </div>
            </div>
            @endforeach
        </form>
        @else
        <div class="container p-0 mt-1">
            <div class="col-md-12 p-0">
                <div class="wrap-contact100 p-0">
                    <div class="container text-black p-2">
                        <p class="text-center text-danger">Kuesioner belum dibuka!.</span></p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @include('pages.pengisian.tataCara')
</body>
    <!-- Script -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/myScript.js') }}"></script>
    <script>
		window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-23581568-13');
	</script>
    <script src="{{ asset('assets1/js/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.3.1/js/fileinput.js"></script>

    <script>
        $('#form').on('submit', function (e) {
            if ($(this)[0].checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            }
            else{
                $('#submitButton').attr('disabled', true);
            }
        });

        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

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
</html>
