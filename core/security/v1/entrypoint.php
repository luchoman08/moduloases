<?php 
/**
 * @package		block_ases
 * @subpackage	core.security
 * @author 		Jeison Cardona Gómez
 * @copyright 	(C) 2019 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license   	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( __DIR__ . "/../../../../../config.php");
require_once( __DIR__ . "/query_manager.php");

/**
 * Function that processes a secure call request.
 *
 * This function requieres a context to execute, an example of this context:
 *
 * $context = array(
 * 	'fun_name' => array( 
 *		'action_alias' => 'one_alias',
 *		'params_alias' => "one_alias"
 *	)
 * )
 *
 * @see get_action( $in ) in this file.
 * @see user_exist( $user_id ) in this file.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param string $function_name, Function name.
 * @param array $args, Arguments.
 * @param array $context, Context for execution.
 * @param integer $user_id, User that execute the function.
 *
 * @return array
 */
function secure_Call( $function_name, $args = null, $context = null, $user_id = null, $current_time = time(), $singularizations = null ){

	//Context validation
	if( is_null( $context ) ){	
		throw new Exception( "Undefined context" ); 
	}else{
		if( !array_key_exists( $function_name , $context) ){
			throw new Exception( "Function '$function_name' does not exist in the context" ); 
		}
	}
	
	//Action validation
	$action = _core_security_get_action( $context[ $function_name ]['action_alias'] );

	if( is_null( $action ) ){
		// Control de no existencia de acción
		/**
		 * En caso de que no exista, se debe registrar en la base de datos
		*/
	}else{

		if( is_null( $user_id ) ){
			throw new Exception( "User rol cannot be null" ); 
		}else{

			if( $user_id <= -1 ){
				return array(
					'status' => -1,
					'status_message' => 'invalid user ID',
					'data_response' => null
				);
			}else{

				if( _core_security_user_exist( $user_id ) ){

					$user_rol = _core_security_get_user_rol( $user_id, $current_time, $singularizations );
					
				}else{
					return array(
						'status' => -1,
						'status_message' => 'invalid user ID',
						'data_response' => null
					);
				}

			}

			/*$defined_user_functions = get_defined_functions()['user'];
			if( in_array( $function_name, $defined_user_functions ) ){
				return call_user_func_array( $function_name, $args );
			}else{
				throw new Exception( "Function " . $function_name . " was not declared." );
			}*/

		}

	}

}

/**
 * Function that returns an action given an id or alias.
 *
 * @see get_db_manager() in query_manager.php
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param mixed $in, this input param can be and id (Integer) or alias (String)
 *
 * @return object|null
*/
function _core_security_get_action( $in ){

	$params = [];
	$criteria = null;
	$tablename = $GLOBALS['PREFIX'] . "talentospilos_acciones";

	if( $in ){
		if( is_numeric($in) ){
			$criteria = "id";
		}else if( is_string( $in ) ){
			$criteria = "alias";
		}else{
			return null;
		}
	}else{
		return null;
	}

	array_push($params, $in);

	$manager = get_db_manager();
	$action = $manager( $query = "SELECT * FROM $tablename WHERE $criteria = $1 AND eliminado = 0", $params, $extra = null );
	return ( count( $action ) == 1 ? $action : null );

}

/**
 * Function that validate if an user exist.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param integer $user_id
 *
 * @return bool
*/
function _core_security_user_exist( $user_id ){

	$params = [];
	$tablename = $GLOBALS['PREFIX'] . "user";

	if( !is_numeric($user_id) ){
		return false;
	}

	array_push($params, $user_id);

	$manager = get_db_manager();
	$user = $manager( $query = "SELECT * FROM $tablename WHERE id = $1", $params, $extra = null );

	return ( count( $user ) == 1 ? true : false );

}


/**
 * Function that return a rol given an user id.
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param integer $user_id
 *
 * @return object|null
*/
function _core_security_get_user_rol( $user_id, $current_time = time(), $singularizations = null ){

	$params = [];
	$tablename = $GLOBALS['PREFIX'] . "usuario_rol";

	if( !is_numeric($user_id) ){
		return false;
	}

	array_push($params, $user_id);

	$manager = get_db_manager();

	$user_roles = $manager( $query = "SELECT * FROM $tablename WHERE id_usuario = $1 AND eliminado = 0", $params, $extra = null );

	//
	$solved_user_roles = [];

	foreach ($user_roles as $key => $u_rol) {
		
		if( 
			( $u_rol->usar_intervalo_alternativo == 0 ) && 
			( !is_null( $u_rol->fecha_hora_inicio ) ) && 
			( !is_null( $u_rol->fecha_hora_fin ) )
		){
			$rol = new stdClass();
			$rol->id = $u_rol['id'];
			$rol->rol_id = $u_rol['id_rol'];
			$rol->start = $u_rol['fecha_hora_inicio'];
			$rol->end = $u_rol['fecha_hora_fin'];
			array_push( $solved_user_roles, $rol );
		}else if( 
			( $u_rol->usar_intervalo_alternativo == 1 ) && 
			( !is_null( $u_rol->usar_intervalo_alternativo ) )
		){
			$alternative_interval = _core_secutiry_solve_alternative_interval( json_decode( $u_rol->intervalo_validez_alternativo ) );
			if( $alternative_interval ){
				$rol = new stdClass();
				$rol->id = $u_rol['id'];
				$rol->rol_id = $u_rol['id_rol'];
				$rol->start = $alternative_interval['fecha_hora_inicio'];
				$rol->end = $alternative_interval['fecha_hora_fin'];
				array_push( $solved_user_roles, $rol );
			}
		}

	}

	print_r($solved_user_roles );

}

/**
 * Function that return an interval given an alternative interval definition.
 * Important!: Prefix is not used here.
 *
 * Example of an alternative interval definition:
 *
 * {
 *		"table_ref": { "name":"table_name", "record_id": 1 },
 *		"col_name_interval_start": "col_start",
 *		"col_name_interval_end": "col_end"
 * }
 *
 * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @since 1.0.0
 *
 * @param json $alternative_interval_json
 *
 * @return array|null
*/
function _core_secutiry_solve_alternative_interval( $alternative_interval_json ){

	$params = [];

	if( 
		property_exists($alternative_interval_json, 'table_ref') && 
		property_exists($alternative_interval_json, 'col_name_interval_start') && 
		property_exists($alternative_interval_json, 'col_name_interval_end')
	){
		if( 
			property_exists($alternative_interval_json->table_ref, 'name') && 
			property_exists($alternative_interval_json->table_ref, 'record_id')
		)
			if( 
				is_numeric($alternative_interval_json->table_ref->record_id) && 
				$alternative_interval_json->table_ref->record_id != ""
			){
				if( 
					$alternative_interval_json->table_ref->record_id >= 0
				){
					$tablename = $alternative_interval_json->table_ref;
					$col_name_interval_start = $alternative_interval_json->col_name_interval_start;
					$col_name_interval_end = $alternative_interval_json->col_name_interval_end;
					$rid = $alternative_interval_json->table_ref->record_id;

					array_push($params, $rid);

					$manager = get_db_manager();

					$query = 
					"SELECT 
						$col_name_interval_start AS fecha_hora_inicio, 
						$col_name_interval_end AS fecha_hora_fin 
					FROM $tablename 
					WHERE id = $1"

					$data = $manager( $query, $params, $extra = null );

					if( count( $data ) == 1 ){
						return array(
							'fecha_hora_inicio' => $data[0]['fecha_hora_inicio'],
							'fecha_hora_fin' => $data[0]['fecha_hora_fin']
						);
					}
				}
			}
		}
	}

	return null;
}

?>