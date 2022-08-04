<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\SatuanModel;
use App\Models\UserModel;

class Barang extends BaseController
{

    protected $model;
    protected $SatuanModel;
    protected $UserModel;
    protected $session;

    function __construct(){
        $this->model = new BarangModel();
        $this->SatuanModel = new SatuanModel();
        $this->UserModel = new UserModel();
        $this->session = session();
    }

    public function index()
    {      
        if ($this->request->getMethod() == 'post') {
            return $this->tambah();
        }

        $count = $this->model->query("SELECT count('barang') FROM barang")->getRowArray();
        $count = $count["count('barang')"];

        $data = [
            'title' => 'Barang',
            'count' => $count,
            'satuan' => $this->model->getSatuan()->getResultArray(),
            'barang' => $this->model->getBarang()->getResultArray(),
            'profile' => $this->profile(),
        ];

        return view('layout/head', $data)
                .view('layout/sidebar')
                .view('layout/nav')
                .view('barang/barang', $data)
                .view('layout/footer');
    }

    public function tambah() {
        
        $input = [
            'barang' => $this->request->getPost('barang'),
            'id_satuan'=> $this->request->getPost('satuan')
        ];

        $this->model->add($input);
        session()->setFlashdata('msg', $this->flash());
        return redirect()->to('/barang');    

    }

    public function edit() {
        $id = $this->request->getPost('id');
        $input = [
            'barang' => $this->request->getPost('barang'),
            'id_satuan'=> $this->request->getPost('satuan')
        ];

        // var_dump($input);die();
        $this->model->update($id, $input);
        session()->setFlashdata('msg', $this->flash());
        return redirect()->to('/barang'); 
    }

    public function tambahSatuan() {
        $input = [
            'satuan' => $this->request->getPost('satuan')
        ];

        $this->SatuanModel->add($input);
        session()->setFlashdata('msg', $this->flash('success', 'Tersimpan', 'Satuan Tersimpan'));
        return redirect()->to('/barang');    
    }

    public function hapus() {
        $id = $this->request->getPost('id');
        // $id = json_encode($id);
        // var_dump();die();
        $this->model->delete($id);
        return redirect()->to('/barang');
    }

    public function profile() {
        $temp = $this->UserModel->user()->getRowArray();
        return $temp['unit_prodi'];
        // var_dump($temp['unit_prodi']);die();
    }

    // default success flashdata can customize
    protected function flash($color = 'success', $title = 'Tersimpan', $msg = 'Data Tersimpan') {
    
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