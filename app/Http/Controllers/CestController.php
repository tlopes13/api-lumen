<?php

namespace App\Http\Controllers;
use App\Cest;
use App\Usuario;
use Illuminate\Http\Request;

class CestController extends Controller
{
	public function carregaCest(Request $data)
	{
		if(!$this->login($data)){
			$json = [ 'cest' => "", 'status' => 3, 'msg' => 'Login Invalido' ];
			return response($json, 401)
			->header('Content-Type', 'application/json');
		}
		$Cest  = new Cest();
		$json  = $Cest->findCest($data);
		$statusCode = 200;
		if( $json['status'] <> 1 ){
			$statusCode = 404;			
		}
		return response($json, $statusCode)
		->header('Content-Type', 'application/json');
	}

	private function login( $data )
	{
		$objUsuario = new Usuario();
		return $objUsuario->logar($data[0]);
	}
}