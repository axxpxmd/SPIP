@php
    $totalRevisi = App\TmResult::select('tm_results.id', 'tm_quesioners.tahun_id')
                    ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
                    ->where('tm_results.user_id', Auth::user()->id)
                    ->where('status_revisi', 1)
                    ->count();

    $role = Auth::user()->modelHasRole->role_id;
    $getUserOpd = App\Models\VerifikatorTempat::where('user_id', Auth::user()->id)->count();
@endphp
<ul class="sidebar-menu">
    <li class="header"><strong>MAIN NAVIGATION</strong></li>
    <li>
        <a href="{{ route('home') }}">
            <i class="icon icon-home red-text s-18"></i>
            <span>Home</span>
        </a>
    </li>
    @can('master-role')
    <li class="header light"><strong>ROLE</strong></li>
    <li class="no-b">
        <a href="{{ route('master-role.permission.index') }}">
            <i class="icon icon-clipboard-list text-red s-18"></i>
            <span>Permission</span>
        </a>
    </li>
    <li>
        <a href="{{ route('master-role.role.index') }}">
            <i class="icon icon-key3 amber-text s-18"></i>
            <span>Role</span>
        </a>
    </li>
    @endcan
    @if (in_array($role, [1,5,6]) && $getUserOpd == 0)
        @can('master-pegawai')
        <li class="header light"><strong>PENGGUNA</strong></li>
        <li class="no-b">
            <a href="{{ route('pengguna.index') }}">
                <i class="icon icon-user-o text-success s-18"></i>
                <span>Pengguna</span>
            </a>
        </li>
        @endcan
    @endif
    @if (in_array($role, [1,5,6]) && $getUserOpd == 0)
        @can('master-data')
        <li class="header light"><strong>DATA</strong></li>
        <li class="no-b">
            <a href="{{ route('waktu.index') }}">
                <i class="icon icon-timer text-blue s-18"></i>
                <span>Waktu</span>
            </a>
        </li>
        <li class="no-b">
            <a href="{{ route('perangkat-daerah.index') }}">
                <i class="icon icon-placeholder text-yellow s-18"></i>
                <span>Perangkat Daerah</span>
            </a>
        </li>
        <li class="no-b">
            <a href="{{ route('indikator.index') }}">
                <i class="icon icon-bookmark-o text-blue s-18 mr-2"></i>
                <span>Indikator</span>
            </a>
        </li>
        <li class="no-b">
            <a href="{{ route('answer.index') }}">
                <i class="icon icon-question_answer text-purple s-18"></i>
                <span>Jawaban</span>
            </a>
        </li>
        <li class="no-b">
            <a href="{{ route('pertanyaan.index') }}">
                <i class="icon icon-question mr-2 text-green s-18"></i>
                <span>Pertanyaan</span>
            </a>
        </li>
        <li class="no-b">
            <a href="{{ route('kuesioner.index') }}">
                <i class="icon icon-document-text2 text-red s-18"></i>
                <span>Quesioner</span>
            </a>
        </li>
        @endcan
        @can('master-pengisian')
        <li class="header light"><strong>PENGISIAN</strong></li>
        <li class="no-b">
            <a href="{{ route('form-quesioner.index') }}">
                <i class="icon icon-document-text6 text-yellow s-18"></i>
                <span>Pengisian Quesioner</span>
            </a>
        </li>
        <li class="no-b">
            <a href="{{ route('hasil.index') }}">
                <i class="icon icon-document-text6 text-success s-18"></i>
                <span>Hasil Quesioner</span>
            </a>
        </li>
        <li class="no-b">
            <a href="{{ route('revisi.index') }}">
                <i class="icon icon-document-text6 text-danger s-18"></i>
                <span>Revisi Quesioner</span> <span class="text-danger font-weight-bold ml-3">{{ $totalRevisi }}</span>
            </a>
        </li>
        @endcan
    @endif
    @can('master-verifikasi')
    <li class="header light"><strong>STATUS PENGISIAN</strong></li>
    <li class="no-b">
        <a href="{{ route('statusPengisian.data', 3) }}">
            <i class="icon icon-percent text-warning s-18"></i>
            <span>Status Pengisian</span>
        </a>
    </li>
    <li class="no-b">
        <a href="{{ route('verifikasi.index') }}">
            <i class="icon icon-verified_user text-blue s-18"></i>
            <span>Verifikasi</span>
        </a>
    </li>
    @endcan
</ul>
