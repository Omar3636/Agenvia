<?php

class FiltroViajes {
    public $nombre_hotel;
    public $ciudad;
    public $pais;
    public $fecha_viaje;
    public $duracion_viaje;
    
    public function __construct() {
        $this->nombre_hotel = '';
        $this->ciudad = '';
        $this->pais = '';
        $this->fecha_viaje = '';
        $this->duracion_viaje = 0;
    }
    
    public function setFiltros($datos) {
        if (isset($datos['hotel'])) {
            $this->nombre_hotel = $datos['hotel'];
        }
        if (isset($datos['ciudad'])) {
            $this->ciudad = $datos['ciudad'];
        }
        if (isset($datos['pais'])) {
            $this->pais = $datos['pais'];
        }
        if (isset($datos['fecha'])) {
            $this->fecha_viaje = $datos['fecha'];
        }
        if (isset($datos['duracion'])) {
            $this->duracion_viaje = $datos['duracion'];
        }
    }
    
    public function getFiltrosActivos() {
        $activos = [];
        
        if ($this->nombre_hotel != '') {
            $activos['hotel'] = $this->nombre_hotel;
        }
        if ($this->ciudad != '') {
            $activos['ciudad'] = $this->ciudad;
        }
        if ($this->pais != '') {
            $activos['pais'] = $this->pais;
        }
        if ($this->fecha_viaje != '') {
            $activos['fecha'] = $this->fecha_viaje;
        }
        if ($this->duracion_viaje > 0) {
            $activos['duracion'] = $this->duracion_viaje;
        }
        
        return $activos;
    }
    
    public function limpiarFiltros() {
        $this->nombre_hotel = '';
        $this->ciudad = '';
        $this->pais = '';
        $this->fecha_viaje = '';
        $this->duracion_viaje = 0;
    }
    
    public function buscarViajes() {
        $viajes_disponibles = [
            [
                'hotel' => 'Hotel Chido',
                'ciudad' => 'Cancún',
                'pais' => 'México',
                'fecha' => '2024-06-15',
                'duracion' => 7,
                'precio' => 850
            ],
            [
                'hotel' => 'Resort Padre',
                'ciudad' => 'California Norte',
                'pais' => 'Peru',
                'fecha' => '2024-07-01',
                'duracion' => 5,
                'precio' => 650
            ],
            [
                'hotel' => 'Hotel Choclo',
                'ciudad' => 'Antofagasta',
                'pais' => 'Chile',
                'fecha' => '2024-05-20',
                'duracion' => 3,
                'precio' => 450
            ]
        ];
        
        $resultados = [];
        
        foreach ($viajes_disponibles as $viaje) {
            $coincide = true;
            
            if ($this->nombre_hotel != '' && 
                stripos($viaje['hotel'], $this->nombre_hotel) === false) {
                $coincide = false;
            }
            
            if ($this->ciudad != '' && 
                strtolower($viaje['ciudad']) != strtolower($this->ciudad)) {
                $coincide = false;
            }
            
            if ($this->pais != '' && 
                strtolower($viaje['pais']) != strtolower($this->pais)) {
                $coincide = false;
            }
            
            if ($this->fecha_viaje != '' && $viaje['fecha'] < $this->fecha_viaje) {
                $coincide = false;
            }
            
            if ($this->duracion_viaje > 0 && $viaje['duracion'] != $this->duracion_viaje) {
                $coincide = false;
            }
            
            if ($coincide) {
                $resultados[] = $viaje;
            }
        }
        
        return $resultados;
    }
    
    public function getCiudadesDisponibles() {
        return ['Cancún', 'California Norte', 'Antofagasta'];
    }
    
    public function getPaisesDisponibles() {
        return ['México', 'Peru', 'Chile'];
    }
    
    public function getDuracionesDisponibles() {
        return [3, 5, 7, 10, 14];
    }
    
    public function mostrarFiltros() {
        echo "<div style='background:#f0f0f0; padding:10px; margin:10px 0;'>";
        echo "<strong>Filtros actuales:</strong><br>";
        echo "Hotel: " . ($this->nombre_hotel ?: 'Cualquiera') . "<br>";
        echo "Ciudad: " . ($this->ciudad ?: 'Cualquiera') . "<br>";
        echo "País: " . ($this->pais ?: 'Cualquiera') . "<br>";
        echo "Fecha: " . ($this->fecha_viaje ?: 'Cualquiera') . "<br>";
        echo "Duración: " . ($this->duracion_viaje ?: 'Cualquiera') . " días<br>";
        echo "</div>";
    }
}
?>