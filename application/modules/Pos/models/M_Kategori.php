<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_Kategori extends CI_Model
{

	function __construct()
	{
		parent::__construct();
//		$this->initialize();
	}

	public function initialize()
	{
		$this->db->query("
			CREATE SCHEMA IF NOT EXISTS pos;	
			CREATE TABLE IF NOT EXISTS pos.kategori (
				id_kategori SERIAL PRIMARY KEY,
				nama_kategori VARCHAR NOT NULL
			);
		");
	}

	function q_data_exists($where){
		return $this->db
				->select('*')
				->where($where)
				->get('pos.kategori')
				->num_rows() > 0;
	}
	function q_data_create($value){
		return $this->db
			->insert('pos.kategori', $value);
	}
	function q_data_update($value, $where){
		return $this->db
			->where($where)
			->update('pos.kategori', $value);
	}
	function q_data_delete($where){
		return $this->db
			->where($where)
			->delete('pos.kategori');
	}
	function q_data_read_where($clause = null){
		return $this->db->query("
			select * from (
				  select
					  a.id_kategori AS id,
					  a.nama_kategori AS text,
					  a.id_kategori,
					  a.nama_kategori
				  from pos.kategori a
			) aa WHERE TRUE
		".$clause);
	}
}
