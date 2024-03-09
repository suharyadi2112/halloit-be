<?php

namespace App\Http\Controllers\Api\Pengaduan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use DataTables;

//model
use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PengaduanController extends Controller
{
    public function GetPengaduan(Request $request){
        
        $perPage = $request->input('per_page');
        $search = $request->input('search');
        $page = $request->input('page');

        try {
        
            $query = Pengaduan::query()->with('detailpengaduan', 'kategoripengaduan','indikatormutu','pelapor', 'workers');
            if ($search) {
                $query->search($search);
            }
            $query->orderBy('created_at', 'desc');
            $getPengaduan = $query->paginate($perPage);

            return response(["status"=> "success","message"=> "Data successfully retrieved", "data" => $getPengaduan], 200);

        } catch (\Exception $e) {

            return response(["status"=> "fail","message"=> $e->getMessage(),"data" => null], 500);
        }
    }

    public function GetPengaduanYajra(){
        try {
            $model = Pengaduan::query()->with('detailpengaduan', 'kategoripengaduan','indikatormutu','pelapor', 'workers');
            return DataTables::eloquent($model)->toJson();
        } 
        catch (\Exception $e) {
            return response()->json(["status"=> "fail","message"=> $e->getMessage(),"data" => null], 500);
        }
    }

    public function GetPengaduanList(){
        try {
            $queryy = Pengaduan::query();
            $getPengaduanList = $queryy->orderBy('created_at', 'desc')->select("id","judul_pengaduan","prioritas")->get(); 
            return response(["status"=> "success","message"=> "Data list pengaduan successfully retrieved", "data" => $getPengaduanList], 200);

        } catch (\Exception $e) {
            return response(["status"=> "fail","message"=> $e->getMessage(),"data" => null], 500);
        }
    }

    public function AssignWorkerToPengaduan(Request $request, $idPengaduan){

        try {

            $pengaduan = Pengaduan::find($idPengaduan);
            if (!$pengaduan) {
                throw new \Exception('Pengaduan not found');
            }

            $pesan = [
                'user_id.required' => 'Workers wajib dipilih.',
                'user_id.min' => 'Wajib memilih worker minimal 1 orang.',
                'user_id.max' => 'Wajib memilih worker maksimal 5 orang.',
            ];

            $validator = Validator::make($request->all(), [
                'user_id' => 'array',
                'user_id' => ['required','min:1','max:5',
                    function ($attribute,$value, $fail) use ($request, $pengaduan) {
                        $failedUsers = [];

                        $assignedUsers = $pengaduan->workers()->pluck('users.id')->toArray();

                        foreach ($request->user_id as $userId) {
                            if (!User::find($userId)) {
                                $failedUsers['user_id_' . $userId] = 'User dengan ID ' . $userId . ' tidak ditemukan.';
                            } elseif (in_array($userId, $assignedUsers)) {
                                // Periksa apakah user sudah di-assign ke pengaduan
                                $failedUsers['user_id_' . $userId] = 'Worker dengan ID ' . $userId . ' sudah diassign ke pengaduan ini sebelumnya.';
                            }
                        }
                        if (!empty($failedUsers)) {
                            return $fail($failedUsers);
                        }

                        if (count(array_unique($value)) !== count($value)) {
                            return $fail('List Workers ' . $attribute . ' duplikat.');
                        }
                    },
                ]
            ], $pesan);

            if ($validator->fails()) {
                return response()->json(["status" => "fail", "message" => $validator->errors(), "data" => null], 400);
            }

            $dataPivot = null;
            DB::transaction(function () use ($request, $pengaduan, &$dataPivot) {
                $idUserAsWorkers = $request->input('user_id'); // Dapatkan semua ID pekerja
                $dataPekerja = [];

                foreach ($idUserAsWorkers as $idUser) {
                $dataPekerja[] = [
                    'user_id' => $idUser,
                    'tanggal_assesment' => date('Y-m-d'),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                }

                $pengaduan->workers()->attach($dataPekerja); // Lampirkan pekerja dengan data pivot

                $dataPivot = Pengaduan::query()->with(['workers' => function ($query) use ($request) {
                $query->select('users.id', 'users.name', 'users.handphone');
                $query->withPivot('tanggal_assesment');
                }])
                ->where('a_pengaduan.id', $pengaduan->id)
                ->get();
            });


            return response(["status"=> "success","message"=> "Assign worker successfully store", "data" => $dataPivot], 200);

        } catch (\Exception $e) {
            return response(["status"=> "fail","message"=> $e->getMessage(),"data" => null], 500);
        }


    }

    public function StorePengaduan(Request $request){
        
        $validator = $this->validatePengaduan($request, null, 'insert');  
        if ($validator->fails()) {
            return response()->json(["status"=> "fail", "message"=>  $validator->errors(),"data" => null], 400);
        }
        try {

            $adminCheck = null;
            if(Auth::user()->jabatan == 'admin'){
                $adminCheck = Auth::user()->id;
            }

            DB::transaction(function () use ($request, $adminCheck) {
                Pengaduan::create([
                    'kode_laporan' => $this->generateCode($request->input('lantai')),
                    'indikator_mutu_id' => $request->input('indikator_mutu_id'),
                    'pelapor_id' => $request->input('pelapor_id'),
                    'admin_id' => $adminCheck,
                    'kategori_pengaduan_id' => $request->input('kategori_pengaduan_id'),
                    'lokasi' => $request->input('lokasi'),
                    'lantai' => $request->input('lantai'),
                    'judul_pengaduan' => $request->input('judul_pengaduan'),
                    'dekskripsi_pelaporan' => $request->input('dekskripsi_pelaporan'),
                    'prioritas' => $request->input('prioritas'),
                    'nomor_handphone' => $request->input('nomor_handphone'),
                    'status_pelaporan' => 'waiting',
                    'tanggal_pelaporan' => date('Y-m-d H:i:s'),
                ]);
            });
            return response()->json(["status"=> "success","message"=> "Pengaduan successfully stored", "data" => $request->all()], 200);

        } catch (\Exception $e) {
            return response()->json(["status"=> "fail","message"=> $e->getMessage(),"data" => null], 500);
        }

    }

    private function validatePengaduan(Request $request, $id, $action = 'insert')
    {   

        $messages = [
            'indikator_mutu_id.required' => 'Indikator mutu wajib diisi.',
            'indikator_mutu_id.max' => 'Indikator mutu max 100 karakter.',
            
            'pelapor_id.required' => 'Pelapor wajib diisi.',
            'pelapor_id.max' => 'Pelapor max 100 karakter.',
            
            'kategori_pengaduan_id.required' => 'Kategori pengaduan wajib diisi.',
            'kategori_pengaduan_id.max' => 'Kategori pengaduan max 100 karakter.',
            
            'lokasi.required' => 'Lokasi wajib diisi.',
            'lokasi.max' => 'Lokasi max 100 karakter.',
            
            'lantai.required' => 'Lantai wajib diisi.',
            'lantai.max' => 'Lantai max 50 karakter.',
            
            'judul_pengaduan.required' => 'Judul pengaduan wajib diisi.',
            'judul_pengaduan.max' => 'Judul pengaduan max 500 karakter.',

            'dekskripsi_pelaporan.required' => 'Deskripsi pelapporan wajib diisi.',
            'dekskripsi_pelaporan.max' => 'Deskripsi pelapporan max 1000 karakter.',
            
            'prioritas.required' => 'Prioritas pelaporan wajib diisi.',
            'prioritas.max' => 'Prioritas pelaporan max 100 karakter.',
            
            'nomor_handphone.max' => 'Nomor handphone max 50 karakter.',
            
            'tanggal_pelaporan.date' => 'Tanggal pelaporan tidak bertipe tanggal(date).',
        ];
        $validator = Validator::make($request->all(), [
            'indikator_mutu_id' => 'required|max:100',
            'pelapor_id' => 'required|max:100',
            'kategori_pengaduan_id' => 'required|max:100',
            'lokasi' => 'required|max:500',
            'lantai' => ['required', 'max:50',
                function ($attribute,$value, $fail) use ($request, $action) {
                    if (!in_array($value, ['basement', '01', '02', '03','04','05','06','07','08', '09', '10'])) {
                        $fail('Lantai input tidak valid (contoh, basement, 01, 02, 03, 04, 05, 06, 07,08 ,09 ,10)');
                    }
                    return true;
                },
            ],
            'judul_pengaduan' => 'required|max:500',
            'dekskripsi_pelaporan' => 'required|max:1000',
            'prioritas' => 'required|max:100',
            'nomor_handphone' => 'max:20',
            'tanggal_pelaporan' => 'date',
        ], $messages);
     
        return $validator;
    }

    private function generateCode($lantai) {
        $kode = date('YmdHis');
        $lantaiPadded = str_pad($lantai, 2, '0', STR_PAD_LEFT);
    
        return $kode . '-' . $lantaiPadded;
    }
}
