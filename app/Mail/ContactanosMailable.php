<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Siniestro;


//agregado por mi para el mail



class ContactanosMailable extends Mailable
{
    use Queueable, SerializesModels;

   
    

    public $siniestro;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($siniestro )
    {
        $this->siniestro = $siniestro;

      
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        // $email =$this->siniestro['emailPas'];
        $siniestro = $this->siniestro['siniestro'];
        $patente = $this->siniestro['patente'];
        
        
       

        $concatenacion = "Estudio DAG - Inspección asignada - Siniestro: $siniestro- Patente: $patente";

        $this->subject($concatenacion)
                ->to('arcichsilvio@gmail.com')
                ->view('emails.contactomail');

               
                

        


        return $this;
        

        
    }
}
