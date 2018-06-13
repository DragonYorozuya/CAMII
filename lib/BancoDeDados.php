<?php
/**Fornece uma forma pratica de interagir com o banco de dados
*@author Gustavo Lucena
*@version 1
*/
class BancoDeDados {
	/**Guarda a conexão com o banco de dados
	 * @var PDO
	 */
	private $conexao;

	/**Gurda os resultados das consultas
	 * @var PDOStatement
	 */
	private $resultado;
	private $ultimoID;

	/**Abre a conexão com o banco de dados
	 * @return true - conexão estabelecida | false - falha na conexão
	 */
	private function conectarNoBancoDeDados() {
		try {
			$dadosDeConexao = "mysql:host=localhost;dbname=CAMII;charset=utf8";
			$this->conexao = new PDO ( $dadosDeConexao, "root", "",[PDO::MYSQL_ATTR_INIT_COMMAND => "SET lc_time_names='pt_BR'"]);
			
		//	$dadosDeConexao = "mysql:host=mysql427.umbler.com;dbname=anime;charset=utf8";
		//	$this->conexao = new PDO ( $dadosDeConexao, "yorozuya", "o2BUgmW+?9R" ,[PDO::MYSQL_ATTR_INIT_COMMAND => "SET lc_time_names='pt_BR'"]);
			
			$this->conexao->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch ( Exception $erro ) {
			echo "Erro na conexão com o banco de dados";
			return false;
		}
		return true;
	}
	/**Fecha a conexão com o banco de dados
	 * @return void
	 */
	private function desconectar() {
		$this->conexao = null;
	}

	
	/**Execulta sentenças sql do tipo SELECT
	 * @param string $slq
	 * @return true - sucesso | false - falha
	 * @example querySelect($sql,[$usuario,$anime] )
	 */
	public function querySelect($sql, $param=null) {
	    if (! $this->conectarNoBancoDeDados ()) {
	        return false;
	    }
	    $this->resultado = null;
	    try {
	        $this->conexao->beginTransaction ();
	        $this->resultado = $this->conexao->prepare ( $sql );
	        
	        $this->resultado->execute ($param);
	    } catch ( PDOException $erro ) {
	        $this->conexao->rollBack ();
	        $this->desconectar ();
	        return false;
	    }
	    $this->conexao->commit ();
	    $this->desconectar ();
	    return true;
	}
	
	/**Execulta sentenças sql do tipo UPDATE, DELETE, INSERT, CREATE TABLE, etc.
	 * @example query($sql,[$usuario,$anime] )
	 * @param string $slq
	 * @return true - sucesso | false - falha
	 */
	public function query($sql, $param=null) {
	    if (! $this->conectarNoBancoDeDados ()) {
	        return false;
	    }
	    try {
	        $this->conexao->beginTransaction ();
	        $this->resultado = $this->conexao->prepare ( $sql );
	        $this->resultado->execute ($param);
	        
	        if (! $this->resultado) {
	            $this->conexao->rollBack ();
	            $this->desconectar ();
	            return false;
	        }
	    } catch ( PDOException $erro ) {
	        $this->conexao->rollBack ();
	        $this->desconectar ();
	        return false;
	    }
	    
	    $this->conexao->commit ();
	    $this->desconectar ();
	    return true;
	}
	/**
	 * Execulta sentenças sql do tipo UPDATE, DELETE, INSERT, CREATE TABLE, etc.
	 *
	 * @param string $slq
	 * @return true - sucesso | false - falha
	 */
	public function queryRetornoID($sql,$param=null) {
	    $this->ultimoID = null;
	    if (! $this->conectarNoBancoDeDados ()) {
	        return false;
	    }
	    
	    try {
	        $this->conexao->beginTransaction ();
	        $this->resultado = $this->conexao->prepare ( $sql );
	        $this->resultado->execute ($param);
	        
	        if (! $this->resultado) {
	            $this->conexao->rollBack ();
	            $this->desconectar ();
	            return false;
	        }
	    } catch ( PDOException $erro ) {
	        $this->conexao->rollBack ();
	        $this->desconectar ();
	        return false;
	    }
	    $this->ultimoID = $this->conexao->lastInsertId ();
	    $this->conexao->commit ();
	    
	    $this->desconectar ();
	    return true;
	}

	/**
	 * Reculpera todos os resultados carregados do banco de dados
	 *
	 * @return array:
	 */
	public function recuperaResultados() {
		return $this->resultado->fetchAll ();
	}

	/**
	 * Reculpera todos os resultados carregados do banco de dados
	 *
	 * @return array:
	 */
	public function ResultadosASSOCAll() {
		return $this->resultado->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Reculpera todos os resultados carregados do banco de dados
	 *
	 * @return array:
	 */
	public function ResultadosASSOC() {
		return $this->resultado->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Retorna a qtde de linhas reculperadas do banco de dados
	 *
	 * @return integer
	 */
	public function recuperaQtdeDeLinhaRetornadas() {
		return $this->resultado->rowCount ();
	}
	public function recuperaUltimoID() {
		return $this->ultimoID;
	}

	
}
?>