<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

// Models
use App\Models\Answer;
use App\Models\Indikator;
use App\Models\Quesioner;

class TmResult extends Model
{
    protected $table = 'tm_results';
    protected $fillable = ['id', 'user_id', 'quesioner_id', 'answer_id', 'keterangan', 'status', 'nilai_akhir', 'nilai_awal', 'message', 'status_kirim', 'status_revisi', 'created_at', 'updated_at', 'answer_id_revisi'];

    public function quesioner()
    {
        return $this->belongsTo(Quesioner::class, 'quesioner_id');
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * QUERY
     */

    //
    public static function getTotal($tahunId, $userId)
    {
        $data = TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->count();

        return $data;
    }

    //
    public static function getTotalVerif($tahunId, $userId)
    {
        $data =  TmResult::join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('status', 1)
            ->where('user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->count();

        return $data;
    }

    // nilai asli dari pertanyaan ( belum diverifikasi )
    public static function getNilai($userId, $tahunId)
    {
        $data =  TmResult::select(DB::raw("sum(tm_answers.nilai) as nilai"))
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->join('tm_answers', 'tm_answers.id', '=', 'tm_results.answer_id')
            ->where('user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->first();

        return $data;
    }

    // nilai yg sudah diverifikasi diambil dari field  nilai_akhir pada tabel tm_results
    public static function getNilaiVerif($userId, $tahunId)
    {
        $data = TmResult::select(DB::raw("sum(nilai_akhir) as nilai_akhir"), DB::raw("sum(nilai_awal) as nilai_awal"))
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('status', 1)
            ->where('user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->where('tm_results.status', 1)
            ->first();

        return $data;
    }

    //
    public static function indikatorArray($userId, $tahunId)
    {
        $data = TmResult::select('tm_quesioners.indikator_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->get()
            ->toArray();

        return $data;
    }

    public static function indikatorArrayRevisi($userId, $tahunId)
    {
        $data = TmResult::select('tm_quesioners.indikator_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->where('tm_results.status_kirim', 0)
            ->get()
            ->toArray();

        return $data;
    }

    //
    public static function questionArray($userId, $tahunId)
    {
        $data = TmResult::select('tm_quesioners.question_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('user_id', $userId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->get()
            ->toArray();

        return $data;
    }

    //
    public static function getDataResult($zonaId, $tahunId, $tempats)
    {
        $data = TmResult::select(DB::raw("SUM(tm_results.status) as total_status"), 'tm_pegawais.nama_instansi', 'tm_results.user_id as id', 'tm_quesioners.tahun_id', 'tm_results.user_id')
            ->join('tm_users', 'tm_users.id', '=', 'tm_results.user_id')
            ->join('tm_pegawais', 'tm_pegawais.user_id', '=', 'tm_users.id')
            ->join('tm_places', 'tm_places.id', '=', 'tm_pegawais.tempat_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('tm_places.zona_id', $zonaId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->where('tm_results.status_kirim', 1)
            ->when($tempats, function ($q) use ($tempats) {
                return $q->whereIn('tm_pegawais.tempat_id', $tempats);
            })
            ->orderBy('total_status', 'DESC')
            ->groupBy('tm_results.user_id')
            ->get();

        return $data;
    }

    //
    public static function getDataResultSekolah($zonaId, $tahunId, $status)
    {
        $data = TmResult::select('tm_pegawais.nama_instansi', 'tm_results.user_id as id', 'tm_quesioners.tahun_id', 'tm_results.user_id')
            ->join('tm_users', 'tm_users.id', '=', 'tm_results.user_id')
            ->join('tm_pegawais', 'tm_pegawais.user_id', '=', 'tm_users.id')
            ->join('tm_places', 'tm_places.id', '=', 'tm_pegawais.tempat_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('tm_places.zona_id', $zonaId)
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->where('tm_results.status_kirim', 1)
            ->groupBy('tm_results.user_id');

        if ($status != null) {
            $data->where('tm_places.status', $status);
        }

        return $data->get();
    }

    //
    public static function getHigherRank($zona_id, $tahun_id)
    {
        $data = TmResult::select(DB::raw("sum(tm_results.nilai_akhir) as nilai"), 'tm_results.user_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->join('tm_indikators', 'tm_indikators.id', '=', 'tm_quesioners.indikator_id')
            ->where('tm_indikators.zona_id', $zona_id)
            ->where('tm_results.status', 1)
            ->where('tm_quesioners.tahun_id', $tahun_id)
            ->groupBy('tm_results.user_id')
            ->get();

        return $data;
    }

    //
    public static function instansiFilled($tahun_id, $zona_id)
    {
        $data = TmResult::join('tm_pegawais', 'tm_pegawais.user_id', '=', 'tm_results.user_id')
            ->join('tm_places', 'tm_places.id', '=', 'tm_pegawais.tempat_id')
            ->join('tm_quesioners', 'tm_quesioners.id', '=', 'tm_results.quesioner_id')
            ->where('tm_places.zona_id', $zona_id)
            ->where('tm_quesioners.tahun_id', $tahun_id)
            ->groupBy('tm_results.user_id')
            ->get()->count();

        return $data;
    }
}
