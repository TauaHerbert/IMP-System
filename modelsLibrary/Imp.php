<?php

class Imp {
    private $id_departamento;
    private $modelo;
    private $ip;
    private $serial;
    private $tipo_cor;
    private $status;


    public function getIdDepartamento() { 
        return $this->id_departamento; 
    }
    
    public function setIdDepartamento($id_departamento) { 
        $this->id_departamento = $id_departamento; 
    }

    public function getModelo() { 
        return $this->modelo; 
    }
    public function setModelo($modelo) { 
        $this->modelo = $modelo; 
    }

    public function getIp() { 
        return $this->ip; 
    }
    public function setIp($ip) { 
        $this->ip = $ip; 
    }

    public function getSerial() { 
        return $this->serial; 
    }
    public function setSerial($serial) { 
        $this->serial = $serial; 
    }

    public function getTipoCor() { 
        return $this->tipo_cor; 
    }
    public function setTipoCor($tipo_cor) { 
        $this->tipo_cor = $tipo_cor; 
    }

    public function getStatus() {
        return $this->status;
    }
    public function setStatus($status) {
        $this->status = $status;
    }

}
?>