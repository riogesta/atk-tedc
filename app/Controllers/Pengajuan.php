<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use App\Models\PengajuanModel;
use App\Models\UserModel;

class Pengajuan extends BaseController
{

    protected $model;
    protected $UserModel;
    protected $session;

    function __construct(){
        $this->model = new PengajuanModel;
        $this->UserModel = new UserModel;
        $this->session = session();
    }

    public function tanggalSekarang() {
        // $time = 43200;
        // $tanggal = strtotime(date("h:i:s A"))+$time;
        // $tanggal = date('Y-m-d', $tanggal);
        $tanggal = Time::now('Asia/Jakarta', 'en_US');
        return $tanggal; //tahun-bulan-tanggal
    }

    public function index()
    {
        if (empty($_SESSION['USER'])) {
           return redirect()->route('login');
        }

        if ($this->request->getMethod() == 'post') {
            $this->tambah();
        }

        if ($_SESSION['ROLE'] == '0') {    
            // jika role  nya adalah user
            $id_unit_prodi = $_SESSION['ID-UNIT-PRODI'];
            $sql = "
            SELECT barang.* , unit_prodi.*, 
            pengajuan.id_pengajuan, pengajuan.id_barang, pengajuan.id_unit_prodi, pengajuan.jumlah, DATE_FORMAT(pengajuan.tanggal, '%d %M %Y') AS tanggal, pengajuan.status  
            FROM pengajuan
            LEFT JOIN barang ON pengajuan.id_barang = barang.id_barang
            LEFT JOIN unit_prodi ON pengajuan.id_unit_prodi = unit_prodi.id_unit_prodi
            WHERE (`pengajuan`.`id_unit_prodi` = $id_unit_prodi AND `pengajuan`.`status` != '3' AND `pengajuan`.`status` != '2' ) ORDER BY tanggal DESC";

            $data = [
                'title' => 'Pengajuan',
                'barang' => $this->model->getBarang()->getResultArray(),
                'pengajuan' => $this->model->getPengajuan()->getResultArray(),
                'status_belum_selesai' => $this->query($sql)->getResultArray()
            ];
            
        } else {
            // jika role adalah admin
            $data = [
                'title' => 'Pengajuan',
                'barang' => $this->model->getBarang()->getResultArray(),
                'pengajuan' => $this->model->getPengajuan()->getResultArray(),
            ];
        }
        


        // var_dump($data['me']);die();

        // change view base on role
        $switch = $_SESSION['ROLE'] == '1' ? 'pengajuan/pengajuan' : 'pengajuan/pengajuan_user';

        return view('layout/head', $data)
                .view('layout/sidebar')
                .view('layout/nav')
                .view($switch, $data)
                .view('layout/footer');
    }

    public function tambah(){

        $input = [
            'id_barang' => $this->request->getPost('barang'),
            'id_unit_prodi' => $_SESSION['ID-UNIT-PRODI'],
            // 'id_satuan' => $this->request->getPost('satuan'),
            'jumlah' => $this->request->getPost('jumlah'),
            'tanggal' => $this->tanggalSekarang()
        ];
        
        if ($this->model->save($input)) {
            session()->setFlashdata('msg', $this->flash());
            return redirect()->to('/pengajuan');           
        } else {
            session()->setFlashdata('msg', $this->flash('red', 'Tidak Terkirim', 'Ajuan tidak terkirim'));
            return redirect()->to('/'); 
        };         

    }

    public function editStatus() {
        
        $id = $this->request->getPost('id');
        $input['status'] = $this->request->getPost('status');

        $this->model->update($id, $input);
        return redirect()->route('pengajuan');
    }

    public function query($sql){
        return $this->model->query($sql);
    }

    // default success flashdata can customize
    protected function flash($color = 'success', $title = 'Terkirim', $msg = 'Ajuan Terkirim') {
    
        $icon = 'check';
        if ($color != 'success') {
            $icon = 'x icon';
        }

        $message = "
        <div class='ui icon $color message' id='message'>
            <i class='$icon icon'></i>
            <div class='content'>
                <div class='header'>
                    $title
                </div>
            <p>$msg</p>
            </div>
        </div>
        ";

        return $message;
    }
}