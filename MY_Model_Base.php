<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*    
    Copyright (C) 2011  Nicola Amatucci - Mercogliano Isidoro

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

class MY_Model_Base extends CI_Model
{
    /**
     * Stringa: nome della classe
     */
    protected $my_class;
    
    /**
     * Array: variabili della classe
     */
    protected $my_class_vars;
    
    /**
     * Variabile utilizzata nella creazione di una nuova istanza
     */
    protected $reflectionObj;    
    
    /**
     * Mantiene lo stato della validazione. Inizializzato a true.
     */
    protected $valido;
    
    /**
     * Array che contiene gli errori
     */
    protected $errori;    
    
    /**
     * Costruttore
     *
     * @param nome tabella
     */
    function __construct() {
        parent::__construct();
        
        //inizializzo l'array degli errori a vuoto
        $this->errori = array();
        $this->valido = true;
        
        //aggiorno parametri utili per la reflection
        $this->my_class = get_class($this);
        $this->my_class_vars = array_diff_key(get_class_vars($this->my_class), array('myclass' => null, 'my_class_vars' => null, 'reflectionObj' => null));
    }
    
    /**
     * Crea e ritorna una nuova istanza della classe
     */
    function newInstance()
    {
        if (!isset($this->reflectionObj))
                $this->reflectionObj = new ReflectionClass($this->my_class);
        
        return $this->reflectionObj->newInstance();
    }
    
    /**
     * Crea una nuova istanza dell'oggetto e la riempie con i dati presi  dal post
     *
     * @return type istanza dell'oggetto
     */
    function newFromPost()
    {        
        $o = $this->newInstance();     

        foreach ($this->my_class_vars as $key => $value)
            if ( isset($_POST[$key]))
                $o->$key = $_POST[$key];

        return $o;
    }
  
    function fillFromPostAndValidate()
    {
        $this->fillFromPost();
        return $this->validate();
    }

    function fillFromPost()
    {
       foreach ($this->my_class_vars as $key => $value)
           if (isset($_POST[$key]))
               $this->$key = $_POST[$key];
   }
    
    /**
     * Ritona l'array degli errori
     */
    function getErrors() { return $this->errori; }
    
    /**
     * Aggiunge un errore all'array errori
     * 
     * @param type nome nell'array
     * @param type messaggio di errore
     */
    function addError($name, $message)
    {
        if (isset($this->errori) == false)
                $this->errori = array();
        
        $this->errori[$name] = $message;
    }

    /**
     * Aggiunge un errore all'array errori
     * e setta valido a false
     * 
     * @param type nome nell'array
     * @param type messaggio di errore
     */
    function addValidationError($field, $message)
    {
        $this->valido = false;        
        $this->addError($field, $message);
    }    
    
    /**
     * Ci dice se l'array errori non è vuoto
     */
    function hasErrors() { return (isset($this->errori) && count($this->errori) > 0); }
    
    /**
     * Ci dice se l'array errori ha un errore con quel nome
     */
    function hasError($name) { return (isset($this->errori) && isset($this->errori[$name])); }    
    
    /**
     * Ritorna l'errore con quel nome
     */
    function getError($name) { return (isset($this->errori) && isset($this->errori[$name]))?$this->errori[$name]:null; }
    
    /**
     * Ritorna l'errore con quel nome
     */
    function getErrorAsString($name) { return (isset($this->errori) && isset($this->errori[$name]))?$this->errori[$name]:""; }    
    
    /**
     * Funzione di validazione
     */
    function validate() { return $this->valid; }
    
    /*
     * setta i valori delle variabili dell'oggetto a null
     */
    function setta_variabili_a_null()
    {
        foreach ($this->my_class_vars as $key => $value)
                $oggetto->$key = null;
    }

    /**
     * Riempie i campi omonimi dell'oggetto corrente con quelli dell'oggetto passato
     * 
     * @param MY_Model $src Oggetto da cui copiare
     */
    function riempi_campi_da_oggetto($src)
    {
        if (isset($src))
        {
            foreach ($this->my_class_vars as $key => $value)
                if (isset($src->$key))
                    $this->$key = $src->$key;
        }          
    }
}
?>
