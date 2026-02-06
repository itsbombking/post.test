<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_Status extends CI_Model
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
			CREATE TABLE IF NOT EXISTS pos.status (
				id_status SERIAL PRIMARY KEY,
				nama_status VARCHAR NOT NULL
			);
		");
	}

	function q_data_exists($where){
		return $this->db
				->select('*')
				->where($where)
				->get('pos.status')
				->num_rows() > 0;
	}
	function q_data_create($value){
		return $this->db
			->insert('pos.status', $value);
	}
	function q_data_update($value, $where){
		return $this->db
			->where($where)
			->update('pos.status', $value);
	}
	function q_data_delete($where){
		return $this->db
			->where($where)
			->delete('pos.status');
	}
	function q_data_read_where($clause = null){
		return $this->db->query("
			select * from (
				  select
				      a.id_status AS id,
					  a.nama_status AS text,
					  a.id_status,
					  a.nama_status
				  from pos.status a
			) aa WHERE TRUE
		".$clause);
	}
}
