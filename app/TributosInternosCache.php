<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class TributosInternosCache extends Model
{
	protected $table = 'tributos_internos_cache';
	private $id_config = 30994;//Basta fixar a pesquisa em um cenário daqueles que calculamos tudo (cenários usados pelo T1, p.ex.)
	private $id_cliente = 55982;//Supermercado Systax
	
	public function buscaRegra($id_cliente, $cod_produto, $origem_produto )
	{
		$arrBind = [
			'id_cliente' 		=> $id_cliente,
			'cod_produto' 		=> $cod_produto,
			'origem_produto' 	=> $origem_produto,
			'id_config' 		=> $this->id_config
		];
		$sql  = "SELECT TOP 1 cache.cod_produto, cache.cest, cache.id_config, cache.id_cliente ";
		$sql .= " FROM systax_app.dbo.tributos_internos_cache cache  WITH (NOLOCK) ";
		$sql .= " WHERE cache.id_cliente = :id_cliente ";
		$sql .= " AND cache.id_config = :id_config ";
		$sql .= " AND cache.cest IS NOT NULL ";
		$sql .= " AND cache.cod_produto = :cod_produto ";
		$sql .= " AND cache.origem_produto = :origem_produto ";
		return DB::select($sql, $arrBind);
	}

	public function buscaCeanRegra($dadosProduto)
	{
		if(is_array($dadosProduto)){
			foreach($dadosProduto as $key => $valores ){
				if(!property_exists($valores, "cod_prod") || empty($valores->cod_prod)){
					continue;
				}
				if(!property_exists($valores, "origem_produto") || !is_numeric($valores->origem_produto)){
					continue;
				}
				$regra = $this->buscaRegra($this->id_cliente, $valores->cod_prod, $valores->origem_produto);
				if($regra){
					return $regra;
				}
			}
		}
		return false;
	}
}