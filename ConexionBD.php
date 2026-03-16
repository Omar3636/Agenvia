<?php
require_once 'config.php';

class ConexionBD {
    private $host = BD_HUESPED;
    private $usuario = BD_USUARIO;
    private $password = BD_CONTRA;
    private $base_datos = BD_NOMBRE;
    private $puerto = BD_PUERTO;
    private $conexion;
    

    public function __construct() {
        $this->conectar();
    }
    
    private function conectar() {
        try {
            $this->conexion = new mysqli(
                $this->host,
                $this->usuario,
                $this->password,
                $this->base_datos,
                $this->puerto
            );
            
            if ($this->conexion->connect_error) {
                throw new Exception("Error de conexión: " . $this->conexion->connect_error);
            }
            
            $this->conexion->set_charset(BD_CHARSET);
            
        } catch (Exception $e) {
            error_log("Fallo de conexión: " . $e->getMessage());
            die("No se pudo conectar a la base de datos.");
        }
    }
    
    public function getConexion() {
        return $this->conexion;
    }

    // Para SELECT
    public function consultaSegura($sql, $params = [], $tipos = "") {
        try {
            $stmt = $this->conexion->prepare($sql);
            
            if ($stmt === false) {
                throw new Exception("Error al preparar consulta: " . $this->conexion->error);
            }
            
            // Vincular parámetros si existen
            if (!empty($params)) {
                $stmt->bind_param($tipos, ...$params);
            }
            
            $stmt->execute();
            $resultado = $stmt->get_result();
            $stmt->close();
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error en consulta: " . $e->getMessage());
            return false;
        }
    }
    
    // Para INSERT/UPDATE/DELETE
    public function ejecutarSeguro($sql, $params = [], $tipos = "") {
        try {
            $stmt = $this->conexion->prepare($sql);
            
            if ($stmt === false) {
                throw new Exception("Error al preparar consulta: " . $this->conexion->error);
            }
            
            if (!empty($params)) {
                $stmt->bind_param($tipos, ...$params);
            }
            
            $resultado = $stmt->execute();
            $id_generado = $stmt->insert_id;
            $stmt->close();
            
            return $resultado ? $id_generado : false;
            
        } catch (Exception $e) {
            error_log("Error en ejecución: " . $e->getMessage());
            return false;
        }
    }
    
    public function escapar($valor) {
        return $this->conexion->real_escape_string($valor);
    }
    
    public function cerrar() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
    
    public function __destruct() {
        $this->cerrar();
    }
}
?>