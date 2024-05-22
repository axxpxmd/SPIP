<!DOCTYPE html>
<html lang="en">
<head>
    <title>SAKIP | LKE</title>
    <link rel="stylesheet" href="{{ public_path('css/util.css') }}">
    <link rel="stylesheet" href="{{ public_path('css/pdf-css.css') }}">

    <style type="text/css">
        .pagenum:before {
            content: counter(page);
        }
        body {
            padding-top: 0px !important;
            color: black !important;
        }
		table.d {
            border-collapse: collapse;
            width: 100%
        }

        table.d tr.d,th.d,td.d{
            table-layout: fixed;
            border: 1px solid black;
            font-size: 10px;
            /* padding-left: 5px */
        }

        .text-center{
            text-align: center
        }

        .p-l-5{
            padding-left: 5px;
        }
        .fs-14{
            font-size: 14px
        }

    </style>
</head>
<body>
    <div class="text-center font-weight-bold fs-14 text-black">
        <p class="m-1">LEMBAR KERJA EVALUASI GABUNGAN</p>
        <p class="m-1"><span class="text-uppercase">{{ $data_user->pegawai->nama_instansi }}</span></p>
        <p class="m-1">TAHUN 2023</p>
    </div>

    <div class="mt-4">
        <table class="d">
            <thead>
                <tr class="d text-center" style="background-color:#172852 !important; color: white !important">
                    <th class="d p-2 fs-14">NO</th>
                    <th class="d p-2 fs-14">KOMPONEN/SUB KOMPONEN/KRITERIA</th>
                    <th class="d p-2 fs-14">BOBOT</th>
                    <th colspan="2" class="d p-2 fs-14">NILAI AKUNTABILITAS</th>
                </tr>
                @php
                    $nilaiAkuntabilitas = 0;
                @endphp
                @foreach ($indikator as $keyInd => $ind)
                    @php
                        $getIdIndikator = App\TmResult::select('tm_quesioners.indikator_id as indikator_id')
                                            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                                            ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
                                            ->where('tm_quesioners.indikator_t', $ind->indikator_t)
                                            ->where('tm_results.user_id', $user_id)
                                            ->where('tm_quesioners.tahun_id', $tahun_id)
                                            ->groupBy('tm_quesioners.indikator_id')
                                            ->get();
                        $totalNilaiIndikator = 0;
                    @endphp
                    @foreach ($getIdIndikator as $indId)
                        @php
                            $getNilaiIndikator = App\TmResult::select(DB::raw("sum(nilai_awal) as nilai_awal"), DB::raw("sum(nilai_akhir) as nilai_akhir"), 'tm_quesioners.bobot_sub_indikator as bobot_sub_indikator', 'pengali_bobot')
                                                ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                                                ->join('tm_questions', 'tm_questions.id', '=', 'tm_quesioners.question_id')
                                                ->where('tm_quesioners.indikator_id', $indId->indikator_id)
                                                ->where('tm_results.user_id', $user_id)
                                                ->where('tm_quesioners.tahun_id', $tahun_id)
                                                ->orderBy('tm_results.status', 'ASC')
                                                ->first();

                            $nilaiAkuntabilitas += App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($getNilaiIndikator->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $getNilaiIndikator->bobot_sub_indikator, $getNilaiIndikator->pengali_bobot );
                            if ($ind->indikator_t == 'Perencanaan Kinerja') {
                                $totalNilaiIndikator += App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($getNilaiIndikator->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $getNilaiIndikator->bobot_sub_indikator, $getNilaiIndikator->pengali_bobot );
                            }elseif($ind->indikator_t == 'Pengukuran Kinerja'){
                                $totalNilaiIndikator += App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($getNilaiIndikator->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $getNilaiIndikator->bobot_sub_indikator, $getNilaiIndikator->pengali_bobot );
                            }elseif($ind->indikator_t == 'Pelaporan Kinerja'){
                                $totalNilaiIndikator += App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($getNilaiIndikator->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $getNilaiIndikator->bobot_sub_indikator, $getNilaiIndikator->pengali_bobot );
                            }elseif($ind->indikator_t == 'Evaluasi Akuntabilitas Kinerja Internal'){
                                $totalNilaiIndikator += App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($getNilaiIndikator->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $getNilaiIndikator->bobot_sub_indikator, $getNilaiIndikator->pengali_bobot );
                            }

                        @endphp
                    @endforeach
                <tr class="d" style="background-color:#84C7E3 !important;">
                    <th class="d text-center">{{ $keyInd+1 }}</th>
                    <th class="d text-uppercase">&nbsp;&nbsp;&nbsp;{{ $ind->indikator_t }}</th>
                    <th class="d text-center">{{ $ind->bobot_indikator }}</th>
                    <th class="d text-center">{{ $totalNilaiIndikator }}</th>
                    <th class="d text-center">{{ $totalNilaiIndikator / $ind->bobot_indikator * 100 }} %</th>
                </tr>
                    @foreach ($getIdIndikator as $subInd => $indId)
                        @php
                            $getNilaiSubIndikator = App\TmResult::select('n_indikator',DB::raw("sum(nilai_awal) as nilai_awal"), DB::raw("sum(nilai_akhir) as nilai_akhir"), 'tm_quesioners.bobot_sub_indikator as bobot_sub_indikator', 'pengali_bobot')
                                            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                                            ->join('tm_indikators', 'tm_indikators.id', '=', 'tm_quesioners.indikator_id')
                                            ->where('tm_quesioners.indikator_id', $indId->indikator_id)
                                            ->where('tm_results.user_id', $user_id)
                                            ->where('tm_quesioners.tahun_id', $tahun_id)
                                            ->orderBy('tm_results.status', 'ASC')
                                            ->first();

                            $nilaiSubIndikator = App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkBobotPertanyaan(App\Http\Controllers\MasterVerifikasi\VerifikasiController::checkNilaiBlade(round($getNilaiSubIndikator->nilai_akhir, PHP_ROUND_HALF_UP, 2)), $getNilaiSubIndikator->bobot_sub_indikator, $getNilaiSubIndikator->pengali_bobot );
                       @endphp
                        <tr>
                            <td class="d text-uppercase text-center">{{ $keyInd+1 }}.{{ chr(65 + $subInd) }}</td>
                            <td class="d ml-3">{{ str_replace($ind->indikator_t . ' - ', "", $getNilaiSubIndikator->n_indikator) }}</td>
                            <td class="d text-uppercase text-center">{{ $getNilaiSubIndikator->bobot_sub_indikator }}</td>
                            <td class="d text-uppercase text-center">{{ $nilaiSubIndikator }}</td>
                            <td class="d text-uppercase text-center">{{ $nilaiSubIndikator / $getNilaiSubIndikator->bobot_sub_indikator * 100 }} %</td>
                        </tr>
                    @endforeach
                @endforeach
                <tr class="d" style="background-color:#172852 !important; color: white !important">
                    <th colspan="3" class="d text-center font-weight-bold text-uppercase p-2 fs-14">Nilai Akuntabilitas Kinerja</th>
                    <th class="d text-uppercase text-center p-2 fs-14">{{ $nilaiAkuntabilitas }}</th>
                    <th></th>
                </tr>
            </thead>
        </table>
    </div>

    <script type="text/php">
        {{-- if (isset($pdf)) {
            $text = "page {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        } --}}
    </script>
</body>

</html>
