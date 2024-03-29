<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table            = 'barang';
    protected $primaryKey       = 'id_barang';
    protected $allowedFields    = ['barang', 'id_satuan'];
    
    public function getBarang($id = null) {

        if ($id == null) {
            return $this->db->query("
            SELECT *
            FROM barang
            INNER JOIN satuan ON barang.id_satuan = satuan.id_satuan
            ");
        }

        return $this->db->query("SELECT * FROM barang WHERE id_barang = '$id'");
    }

    public function query($sql = null){
        return $this->db->query($sql);
    }

    public function getSatuan($id = null) {

        if ($id == null) {
            return $this->db->query("SELECT * FROM satuan");
        }

        return $this->db->query("SELECT * FROM satuan WHERE id_satuan = '$id'");
    }

    public function add($input) {
        return $this->save($input);
    }
}