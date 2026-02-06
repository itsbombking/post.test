<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_Produk extends CI_Model
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
			
			CREATE TABLE IF NOT EXISTS pos.status (
				id_status SERIAL PRIMARY KEY,
				nama_status VARCHAR NOT NULL
			);
			
			CREATE TABLE IF NOT EXISTS pos.produk (
				id_produk SERIAL PRIMARY KEY,
				nama_produk VARCHAR NOT NULL,
				harga INTEGER NOT NULL,
				kategori_id INTEGER NOT NULL,
				status_id INTEGER NOT NULL,
				CONSTRAINT fk_produk_kategori
					FOREIGN KEY (kategori_id)
					REFERENCES pos.kategori(id_kategori),
				CONSTRAINT fk_produk_status
					FOREIGN KEY (status_id)
					REFERENCES pos.status(id_status)
			);
		");
	}

	function q_data_exists($where){
		return $this->db
				->select('*')
				->where($where)
				->get('pos.produk')
				->num_rows() > 0;
	}
	function q_data_create($value){
		return $this->db
			->insert('pos.produk', $value);
	}
	function q_data_update($value, $where){
		return $this->db
			->where($where)
			->update('pos.produk', $value);
	}
	function q_data_delete($where){
		return $this->db
			->where($where)
			->delete('pos.produk');
	}
	function q_data_read_where($clause = null){
		return $this->db->query("
			select 
				*,
				'Rp'||translate(
						to_char(aa.harga, 'FM999,999,999,999.00'),
						',.',
						'.,'
				) AS harga_format
			from (
				  select
					  a.id_produk AS id,
					  a.nama_produk AS text,
					  a.id_produk,
					  a.nama_produk,
					  a.harga,
					  a.kategori_id,
					  a.status_id,
					  b.nama_kategori,
					  c.nama_status
				  from pos.produk a
				  LEFT OUTER JOIN pos.kategori b ON a.kategori_id = b.id_kategori
				  LEFT OUTER JOIN pos.status c ON a.status_id = c.id_status
				) aa WHERE TRUE
		".$clause);
	}
}
