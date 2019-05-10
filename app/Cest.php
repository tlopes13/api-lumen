<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Cest extends Model
{
    protected $table = 'cest';
    protected $fillable = ['descricao','cest','ncm'];
    
	public function carregaCest( $cestCode, $ex_tipi = NULL )
	{
		$sql  = "SELECT TOP 1 id, cest, descricao, ncm ";
		$sql .= "FROM systax_regras..cest WITH (NOLOCK) ";
		$sql .= "WHERE cest = '".trim($cestCode)."' ";
		if($ex_tipi){
			$sql .= "AND ex_tipi = '".trim($cestCode)."' ";
		} else{
			$sql .= "AND ex_tipi IS NULL ";			
		}
		return DB::select($sql);		
	}

	public function findCest( $data )
	{
		if($this->validaData($data) == false){
			return  ['cest' => "", 'status' => 4, 'msg' => 'EAN ou NCM não foram Informados'];
		}
		$regra = FALSE;
		if( !empty($data[0]['ean'])){
			$objProduto = new CustomProd();
			//Pelo EAN temos de pesquisar  na BC e na EAN_relacionados.
			$customProd = $objProduto->carregaProdutoPorEan($data[0]['ean']);
			if( $customProd ){
				$objTributosInternosCache = new TributosInternosCache();
				//Retornar direto o CEST que já temos na regra que geramos (na cache).
				//Como o CEST não muda em razão do cenário, basta fixar a pesquisa em um cenário daqueles que calculamos tudo (cenários usados pelo T1, p.ex.)
				$regra = $objTributosInternosCache->buscaCeanRegra($customProd);
			}
		}
		if($regra){
			return  [ 'cest' => $regra[0]->cest, 'status' => 1, 'msg' => 'OK' ];
		} else {
			// Olhar na nossa tabela de CEST, se o CEST informado na chamada é possível para  NCM+EX_TIPI informada na chamada.
			if( !empty($data[0]['cest']) && !empty($data[0]['ncm'])){
				$result = $this->carregaCest($data[0]['cest'], $data[0]['ex_tipi']);
				if( !empty($result[0]) ){
					$ncm_compativel = (trim($data[0]['ncm']) == trim($result[0]->ncm));
					if($ncm_compativel==false){
						return  ['cest' => "", 'status' => 2, 'msg' => 'Combinação de cest e ncm inválida'];
					}
					return ['cest' => $result[0]->cest, 'status' => 1, 'msg' => 'OK.'];
				}
			}
		}
		return  ['cest' => "", 'status' => 2, 'msg' => 'Combinação de cest e ncm inválida'];
	}

	public function validaData( $data )
	{
		if( empty($data[0]['ean']) || empty($data[0]['ncm'])){
			return false;
		}
		return true;
	}
}