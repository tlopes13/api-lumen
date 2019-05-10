<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
	protected $table = 'usuarios';

	function logar($data)
	{
		if(empty($data['username']) || empty($data['senha'])){
			return false;
		}
		$arrBind    = [
			'username'  => trim($data['username']),
			'senha'		=> md5(trim($data['senha']))
		];
		$sql  = "SELECT TOP 1 * ";
		$sql .= "FROM systax_app.dbo.usuarios u WITH (NOLOCK)  ";
		$sql .= "INNER JOIN systax_app.dbo.usuario_clientes uc WITH (NOLOCK) on (u.id = uc.id_usuario) ";
		$sql .= "INNER JOIN systax_app.dbo.licencas_controle lc WITH (NOLOCK) on (lc.id_usuario = u.id) ";
		$sql .= "WHERE u.username = :username AND u.senha = :senha AND u.deletado = 0 AND lc.dt_expiracao > GETDATE() ";
		return DB::select($sql, $arrBind);
	}
}