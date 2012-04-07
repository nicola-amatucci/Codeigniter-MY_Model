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

class MY_Model extends CI_Model
{
    /**
     * Nome della tabella associata al Model
     */
    protected $MY_TABLE;
    function setMyTable($TABLE) { if (isset($TABLE)) $this->MY_TABLE = $TABLE; }
    function getMyTable() { return $this->MY_TABLE; }
        
    /**
     * Stringa: nome della classe
     */
    protected $my_class;
    
    /**
     * Array: variabili della classe
     */
    protected $my_class_vars;
    
    /**
     * Costruttore
     *
     * @param nome tabella
     */
    function __construct($TABLE = null) {
        parent::__construct();
        $this->setMyTable($TABLE);
        
        //inizializzo l'array degli errori a vuoto
        $this->errori = array();
        $this->valido = true;
        
        //aggiorno parametri utili per la reflection
        $this->my_class = get_class($this);
        $this->my_class_vars = array_diff_key(get_class_vars($this->my_class), array('myclass' => null, 'my_class_vars' => null, 'reflectionObj' => null, 'MY_TABLE' => null));
    }
    
    /**
     * Variabile utilizzata nella creazione di una nuova istanza
     */
    protected $reflectionObj;
    
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
     * Variabile che identifica un record nella tabella
     */
    var $id;

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
     * A partire da una row restituita dalla libreria db di codeigniter
     * crea un'oggetto che può essere usato client side
     * 
     * @param row della libreria db di codeigniter
     * @return istanza oggetto
     */
    function newFromRow($row)
    {
        $o = $this->newInstance();

        foreach ($this->my_class_vars as $key => $value)
            if (isset($row->$key))
                $o->$key = $row->$key;
            
        return $o;
    }

    /**
     * Crea una nuova istanza dell'oggetto e la riempie con i dati presi  dal post
     *
     * @return type istanza dell'oggetto
     */
    function fillFromRow($row)
    {        
        foreach ($this->my_class_vars as $key => $value) {
            if (isset($row->$key))
                $this->$key = $row->$key;
        }
    }
  
    /**
     * Validazione dell'id
     */
    protected function id_valido($str = null)
    {
        return (isset($str) && $str != "" && preg_match( '/^\d*$/', $str) == 1);
    }

    /**
     * Mantiene lo stato della validazione. Inizializzato a true.
     */
    protected $valido;
    
    /**
     * Array che contiene gli errori
     */
    protected $errori;
    
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
    
    /**
     * Conserva l'esito dell'ultimo salvataggio -> da implementare
     */
    protected $salvato;
    
    /**
     * Operazioni da effettuare prima dell'inserimento (ad esempio conversioni
     * di date)
     */
    function before_inserisci() {}

    /**
     * Operazioni da effettuare dopo l'inserimento (ad esempio conversione
     * di date nel formato di inserimento)
     */
    function after_inserisci() {}
        
    /**
     * Effettua l'inserimento e ritorna l'oggetto riempito con l'id se l'inserimento
     * è andato a buon fine
     */
    function inserisci()
    {
        $this->before_inserisci();
	
        //correzione dei valori null (vengono settati a stringa vuota dal post)        
        foreach ($this->my_class_vars as $key => $value)
          if ($this->$key!=0 && $this->$key=="")
                $this->$key=null;        

        $ok = $this->db->insert($this->MY_TABLE, $this);
        
    	if ($ok)
            $this->id = $this->db->insert_id();
    
        $this->after_inserisci();
        
    	return $ok;
    }

    /**
     * Operazioni da effettuare prima dell'aggiornamento (ad esempio conversioni
     * di date)
     */
    function before_aggiorna() {}

    /**
     * Operazioni da effettuare dopo l'aggiornamento (ad esempio conversione
     * di date nel formato di inserimento)
     */
    function after_aggiorna() {}
    
    /**
     * Effettua l'aggiornamento. L'id nel model deve essere settato ed esistente
     */
    function aggiorna()
    {
        if($this->id_valido($this->id) && $this->esiste($this->id))
    	{
            $this->before_aggiorna();

            //correzione dei valori null (vengono settati a stringa vuota dal post)        
            foreach ($this->my_class_vars as $key => $value)
                if ($this->$key!=0 && $this->$key=="")
                    $this->$key=null;        
            
	    $this->db->where('id', $this->id);
	    $esito = $this->db->update($this->MY_TABLE, $this);
            
            $this->after_aggiorna();
            
            return $esito;
    	}
    	else
    	{            
            return false;
    	}
    }

    /**
     * Operazioni da effettuare prima del salvataggio (ad esempio conversioni
     * di date)
     */
    function cerca_prima_di_salvare() {
        return $this->cerca_da_id();
    }

    /**
     * Effettua il salvataggio, ovvero l'inserimento o l'aggiornamento.
     * L'aggiornamento viene fatto se il record esiste
     */
    function salva()
    {
        $o = $this->cerca_prima_di_salvare();

    	if (isset($o))
    	{
            //nota: faccio così perché cerca_prima_di_salvare potrebbe non
            //      cercare per id
            $this->id = $o->id;
            return $this->aggiorna();
    	}
    	else
    	{
            return $this->inserisci();
    	}
    }

    /**
     * Effettua la validazione e poi il salvataggio, se la validazione ha successo
     */
    function valida_e_salva()
    {
    	$errore = $this->validate();

        if (count($errore) == 0)
        {
            if (!$this->salva())
            {
                $errore['salvataggio'] = "Il salvataggio dei dati non &egrave; andato a buon fine!";
            }
        }

        return $errore;
    }


    /**
     * Effettua l'eliminazione. L'id nel model deve essere settato ed esistente
     */
    function elimina($id = null)
    {
        if (!isset($id))
            $id = $this->id;

        if ($this->id_valido($id) && $this->esiste($id))
        {
            $this->db->where('id', $id);
            return $this->db->delete($this->MY_TABLE);
            //return ($this->db->affected_rows() > 0);
        }
        else
        {
            return false;
        }
    }

    /**
     * Controlla l'esistenza di un record con l'id passato o quello del Model
     */
    function esiste($id = null)
    {
        if (!isset($id))
            $id = $this->id;

        if ($this->id_valido($id))
        {
            $this->db->where('id', $id);
            $this->db->from($this->MY_TABLE);
            $query = $this->db->get();
                        
            return ($query->num_rows() > 0);
        }

        return false;
    }        
    
    /**
      * Esegue una query di lettura settata con ActiveRecord restituendo la prima
      * riga e riepiendo da essa l'oggetto $this (cioè il model)
      * ps:.... si potrebbe utilizzare questo anzicchè il precedente nel cerca_da_id
      */
    function fillThis() {
        $this->db->from($this->MY_TABLE);
        $query_r = $this->db->get();

        if ($query_r->num_rows() > 0)
        {
            $riga = $query_r->row();
            
            foreach ($this->my_class_vars as $key => $value)
                if (isset($riga->$key))
                    $this->$key = $riga->$key;

            $this->after_select();
                
            return true;
        }
        else
        {
            return false;
        }
    }    
        
    /**
     * Azioni da intraprendere dopo una query di lettura
     */
    function after_select() {}
    
    /**
     * Esegue una query e ritorna un'istanza di un oggetto
     * 
     * 
     * @return istanza di un oggetto 
     */
    function getRow()
    {
        $this->db->from($this->MY_TABLE);
        $query_result = $this->db->get();

        if ($query_result->num_rows() > 0)
        {
            $r = $this->newFromRow($query_result->row());
            $r->after_select();            
            return $r;
        }
        return null;
    }

    /**
     * Esegue una query e ritorna un array di istanze
     * 
     * @return array di istanze
     */
    function getResult()
    {
        $this->db->from($this->MY_TABLE);
        $query_result = $this->db->get();

        $result = Array();

        if ($query_result->num_rows() > 0)
        {
            foreach ($query_result->result() as $row)
            {
                $r = $this->newFromRow($row);
                $r->after_select();
                $result[] = $r;
            }
        }

        return $result;
    }    
    
    /**
     * Esegue una query di lettura testuale restituendo la prima
     * riga
     */
    function queryRow($query)
    {
        if (isset($query) && $query != "")
        {
            $query_r = $this->db->query($query);

            if ($query_r->num_rows() > 0)
            {
                $r = $this->newFromRow($query_r->row());
                $r->after_select();
                return $r;
            }
        }

        return null;
    }

    /**
     * Esegue una query di lettura testuale restituendo la prima
     * riga
     */
    function queryResult($query)
    {
        $result = Array();
        
        if (isset($query) && $query != "")
        {
            $query_r = $this->db->query($query);

            if ($query_r->num_rows() > 0)
                foreach ($query_r->result() as $row)
                {
                    $r = $this->newFromRow($row);
                    $r->after_select();
                    $result[] = $r;
                }
        }

        return $result;
    }

    /**
     * Restituisce tutti i record nella tabella
     */
    function seleziona_tutti($order_by = null)
    {
    	if (isset($order_by))
    		$this->db->order_by($order_by);

    	return $this->getResult();
    }

    /**
     * Restituisce un record identificato da $id
     */
    function cerca_da_id($id = null)
    {
        if (!isset($id))
            $id = $this->id;

    	if($this->id_valido($id))
    	{
            $this->db->where("id", $id);
            return $this->getRow();
    	}

    	return null;
    }    
    
    /*
     * setta i valori delle variabili dell'oggetto a null
     */
    function setta_variabili_a_null()
    {
        foreach ($this->my_class_vars as $key => $value)
                $oggetto->$key = null;
    }

    /**
     * Fornisce in output un array di oggetto che corrispondono a ai valori settati dell'oggetto
     * 
     * @param string lista di parametri secondo cui ordinare il risultato
     * @return array di istanze o array vuoto
     */
    function ricerca_da_parametri_settati($order_by = null)
    {
        foreach ($this->my_class_vars as $key => $value) {
            if (isset($this->$key))
                $this->db->where($key, $this->$key);
        }        

    	if (isset($order_by))
            $this->db->order_by($order_by);

        $result = $this->getResult();
        $this->firephp->log($this->db->last_query(), "last_query()");
        
        return $result;
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
