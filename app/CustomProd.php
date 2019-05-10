<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CustomProd extends Model
{
	protected $table = 'custom_prod';
	protected $fillable = ['cod_prod','cean14_med','ncm'];

	public function carregaProdutoPorEan( $ean )
	{
		$cean14_med = str_pad( trim($ean), 14, "0", STR_PAD_LEFT);
		$arrBind    = [
			'cean14_med' => $cean14_med, 
			'cean14_med2' => "{$cean14_med}%"
		];
		$sql  = "";
		$sql .= "	SELECT cp.cod_prod, cp.origem_produto, cp.cean14_med";
		$sql .= " 	FROM systax_app.dbo.custom_prod cp WITH (NOLOCK) ";
		$sql .= " 	WHERE cp.id_cli = 55982 AND cp.status > 0 AND cp.cean14_med = :cean14_med ";
		$sql .= "	UNION ";
		$sql .= " 	SELECT TOP 1 prod.cod_prod, prod.origem_produto, rel.cean14_vinculado";
		$sql .= "   FROM systax_app.dbo.custom_prod prod WITH (NOLOCK)";
		$sql .= " 	INNER JOIN systax_app.dbo.cean_relacionado rel WITH (NOLOCK) ON (rel.cean14_padrao = prod.cean14 )";
		$sql .= " 	WHERE prod.id_cli = 55982 AND prod.status > 0 and isnull(prod.flag_base_nao_usar,0) <> 1";
		$sql .= " 	AND rel.cean14_vinculado LIKE :cean14_med2 ";
		return DB::select($sql, $arrBind);
	}

	public function geraCean14( $ean, $origem_produto = 0)
	{
		$arrValorOrigem = array( '8' => '3', '5' => '0' );
		$origem_produto = ( !is_numeric($origem_produto) || empty($origem_produto) || $origem_produto == 5 ? '0' : $origem_produto );
		$cean14  		= str_pad( $ean, 14, "0", STR_PAD_LEFT);
		if(isset($arrValorOrigem[$origem_produto])){
			$cean14 .= $arrValorOrigem[$origem_produto];
		} else{
			$cean14 .= $origem_produto;
		}
		return $cean14;
	}
}