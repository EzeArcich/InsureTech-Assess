<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//PHP mailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


//mail
use App\Mail\ContactanosMailable;
use Illuminate\Support\Facades\Mail;
use App\Models\Siniestro;





class CoordinacionesController extends Controller
{
    public function index(){
        return view('contactanos.index');
    }

    public function store(Request $request){

         
         $siniestro = Siniestro::paginate(1000);

         $this->siniestro = $siniestro;
         
        
        // $email = $this->siniestro['mailperito'];
        

        $correo = new ContactanosMailable($request->all());
        Mail::to('test-oomo0v99w@srv1.mail-tester.com')->send($correo);

        return redirect()->route('siniestros.index');

        
    }

    public function contacto(Request $request){

         
        $siniestro = Siniestro::paginate(1000);

        $this->siniestro = $siniestro;
        
       
    //    $email = $this->siniestro['mailPAS'];

       
       

       $correo = new ContactanosMailable($request->all());
       Mail::to('test-oomo0v99w@srv1.mail-tester.com')->send($correo);

       return redirect()->route('siniestros.index');

       
   }

    public function correo(Request $request){ // Request $request

        $siniestro = $request->siniestro;
        $cc = $request->cc;
        $cc2 = $request->cc2;
        $nrocorto = $request->nrocorto;
        $email = $request->emailPas;
        $patente = $request->patente;
        $usuario = $request->coordinador;
        $file = $request->file;

        $asunto = "Estudio DAG - Siniestro: $siniestro- Patente: $patente - $nrocorto";

        $cuerpodelmail = "<div class=>  
        <div class=>
            <div class=>
                <div class=>
                    <div class=>
                                
                                 
                                <div id=':mdi' class='Am Al editable LW-avf tS-tW' hidefocus='true' aria-label='Cuerpo del mensaje' g_editable='true' role='textbox' aria-multiline='true' contenteditable='true' tabindex='1' style='direction: ltr; min-height: 226px;' spellcheck='false'><div dir='ltr'><div><div dir='ltr'><div dir='ltr'><div><div dir='ltr'><div><div dir='ltr'><div dir='ltr'><div>Estimados,<br><br>me comunico desde Estudio DAG, al servicio de Mercantil Andina, a fin de coordinar una inspeccion del rodado patente <span zeum4c268='PR_3_0' data-ddnwab='PR_3_0' aria-invalid='spelling' class='LI ng'> $patente </span>.<br><br>Favor de aportar telefono del titular registral del rodado para coordinar la misma o informar los siguientes datos:<br><br>En caso de taller homologado , adjunto listado completo, quedando al aguardo de que nos indican taller y fecha por el cual&nbsp;
    
    pasara el asegurado<br><span zeum4c268='PR_10_0' data-ddnwab='PR_10_0' aria-invalid='grammar' class='Lm ng'>Tengan</span> en cuenta que no deben de coordinar turno con el taller; nos lo solicitan directamente por este medio:<br><br> <a href='https://siniestrosdag.com/urls/1654551972_TALLERES%20HOMOLOGADOS%20-%20ACTUALIZADO.xlsx'>https://siniestrosdag.com/urls/1654551972_TALLERES%20HOMOLOGADOS%20-%20ACTUALIZADO.xlsx</a>  <br>(En el caso de que el link no <span zeum4c268='PR_4_0' data-ddnwab='PR_4_0' aria-invalid='grammar' class='Lm ng'>funcione</span>, copiar y pegar en su navegador para descargar el archivo)<br><br>En caso de optar por un taller que no este en el listado, lo ideal es que saque turno, y nos lo informe por este medio con al menos 24 hs de antelacion, indicando fecha, domicilio, localidad, telefono y correo electronico del taller.<br><br>En caso de que el asegurado informe no tener piezas por reparar, pueden <span zeum4c268='PR_13_0' data-ddnwab='PR_13_0' aria-invalid='spelling' class='LI ng'>adjuntarnos</span> fotos por este medio.<br><br>Por cualquier consulta, quedo a entera disposicion.<br><br>Saludos cordiales.<br><br><br><br></div></div></div></div></div></div></div></div></div></div><div><div dir='ltr' class='gmail_signature' data-smartmail='gmail_signature'><div dir='ltr'><div><div><span style='color:rgb(34,34,34)'>$usuario</span><div style='color:rgb(34,34,34)'><font style='font-size:10pt'>
    Coordinacion de siniestros</font></div><div style='color:rgb(34,34,34)'><font style='font-size:10pt'>ESTUDIO DAG</font></div>
    <div style='color:rgb(34,34,34)'><a href='http://www.estudiodag.com.ar/' style='color:rgb(17,85,204)' target='_blank'>
    <font style='font-size:10pt'>www.estudiodag.com.ar</font></a></div></div></div></div></div></div></div>
    
                                </div>
                                
                                <div>

                                
                                
                          
                    </div>
                </div>
            </div>
        </div>
    </div>";

        @require '/home/u277224827/domains/siniestrosdag.com/dash_roles test/vendor/autoload.php';
        @require '/home/u277224827/domains/siniestrosdag.com/dash_roles test/vendor/phpmailer/phpmailer/src/Exception.php';
        @require '/home/u277224827/domains/siniestrosdag.com/dash_roles test/vendor/phpmailer/phpmailer/src/PHPMailer.php';
        @require '/home/u277224827/domains/siniestrosdag.com/dash_roles test/vendor/phpmailer/phpmailer/src/SMTP.php';
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;  
        $mail->Username = 'coordinaciones.dag@gmail.com';
        $mail->Password = 'hvmhibwglsibihzu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('derivaciones@siniestrosdag.com', 'Coordinaciones - Estudio DAG');
        $mail->addAddress($email);


        
        if(empty($cc)) {
            
            global $cc;
            $cc = $email;
        };

        if(empty($cc2)) {
            global $cc2;
            $cc2 = $email;
            
        };


        $mail->AddCC($cc);
        $mail->AddCC($cc2);
        


        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $cuerpodelmail;
        $dt = $mail->Send();

                if($dt){

                    return redirect()->back()
                    ->with('success', 'Created successfully!');;
                } else{
                    echo 'Mensaje '. $mail->ErrorInfo;
                }
        

        }

        public function correoEdu(Request $request){ // Request $request

            $siniestro = $request->siniestro;
            $email = $request->email;
            $patente = $request->patente;
            $fechaip = $request->fechaip;
            $nrocorto = $request->nrocorto;
            $comentariosparaip = $request->comentariosparaip;
            $telefono = $request->telefono;
            $localidad = $request->localidad;
            $direccion = $request->direccion;
            $modalidad = $request->modalidad;
            $imagen = $request->imagen;
            $link = $request->link;
            $lugar = $request->lugar;
            // $nombretaller = $request->nombretaller;
            $motivo = $request->motivo;
            $horario = $request->horario;
            $cliente = $request->cliente;
            $enviarorden = $request->enviarorden;
            $contacto = $request->contacto;
    
            $asunto = "Inspeccion - Siniestro: $siniestro- Patente: $patente $nrocorto // $fechaip";
    
            $cuerpodelmail = "<tbody style='margin-bottom:200px'><tr height='20' style='height:15pt'>
            <td height='20' width='34' style='height:15pt;width:26pt;color:rgb(255,230,153);border-top:1pt solid windowtext;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td width='161' style='width:121pt;color:rgb(255,230,153);border-top:1pt solid windowtext;border-right:none;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td width='209' style='width:157pt;color:rgb(255,230,153);border-top:1pt solid windowtext;border-right:none;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td width='29' style='width:22pt;color:rgb(255,230,153);border-top:1pt solid windowtext;border-right:none;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td width='128' style='width:96pt;color:rgb(255,230,153);border-top:1pt solid windowtext;border-right:none;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td width='181' style='width:136pt;color:rgb(255,230,153);border-top:1pt solid windowtext;border-right:none;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td width='21' style='width:16pt;color:rgb(255,230,153);border-top:1pt solid windowtext;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='21' style='height:15.75pt'>
            <td height='21' style='height:15.75pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Fecha de inspeccion&nbsp;</td>
            <td style='border-left:none;font-size:12pt;border-top:0.5pt solid windowtext;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>$fechaip</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Lugar de inspeccion</td>
            <td style='border-left:none;font-size:12pt;border-top:0.5pt solid windowtext;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>$lugar</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='20' style='height:15pt'>
            <td height='20' style='height:15pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='21' style='height:15.75pt'>
            <td height='21' style='height:15.75pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Rango Horario</td>
            <td style='border-left:none;font-size:12pt;border-top:0.5pt solid windowtext;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>$horario</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Direccion</td>
            <td style='font-size:12pt;font-weight:700;font-family:Arial,sans-serif;padding-top:1px;padding-right:1px;padding-left:1px;vertical-align:bottom;border:none;white-space:nowrap'>$direccion</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='20' style='height:15pt'>
            <td height='20' style='height:15pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='21' style='height:15.75pt'>
            <td height='21' style='height:15.75pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Tipo de inspeccion</td>
            <td style='border-left:none;font-size:12pt;border-top:0.5pt solid windowtext;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>$modalidad</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Localidad</td>
            <td style='font-size:12pt;font-weight:700;font-family:Arial,sans-serif;padding-top:1px;padding-right:1px;padding-left:1px;vertical-align:bottom;border:none;white-space:nowrap'>$localidad</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='20' style='height:15pt'>
            <td height='20' style='height:15pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='21' style='height:15.75pt'>
            <td height='21' style='height:15.75pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Motivo</td>
            <td style='border-left:none;font-size:12pt;border-top:0.5pt solid windowtext;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>$motivo</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>TE</td>
            <td style='color:rgb(5,99,193);text-decoration-line:underline;padding-top:1px;padding-right:1px;padding-left:1px;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'><span aria-label='Llamar al número de teléfono 011 5303-5997'><a href='https://www.google.com/search?rlz=1C1CHBF_esAR1018AR1018&amp;q=Campomar+Olivos&amp;si=AC1wQDCb48pJOhjniU-CPpWXcWQCAuOVlcIjSvs_FGbLklR5dsiy-7ZahwqTqJJAkXk4QSfb8Upo6FfKYDLzZD6o4ZXJa4l5h3XSwd-GQxrnH6fzIzwWSThXWDm5tC461JTEpJyvUQTrlnRSLtHfaxU3kwJH4PPeWg%3D%3D&amp;sa=X&amp;ved=2ahUKEwiJiqHQ1aj6AhVVtJUCHeR6AxcQ6RN6BAgmEAE&amp;biw=1476&amp;bih=676&amp;dpr=1.25' style='outline:0px' target='_blank' data-saferedirecturl='https://www.google.com/url?q=https://www.google.com/search?rlz%3D1C1CHBF_esAR1018AR1018%26q%3DCampomar%2BOlivos%26si%3DAC1wQDCb48pJOhjniU-CPpWXcWQCAuOVlcIjSvs_FGbLklR5dsiy-7ZahwqTqJJAkXk4QSfb8Upo6FfKYDLzZD6o4ZXJa4l5h3XSwd-GQxrnH6fzIzwWSThXWDm5tC461JTEpJyvUQTrlnRSLtHfaxU3kwJH4PPeWg%253D%253D%26sa%3DX%26ved%3D2ahUKEwiJiqHQ1aj6AhVVtJUCHeR6AxcQ6RN6BAgmEAE%26biw%3D1476%26bih%3D676%26dpr%3D1.25&amp;source=gmail&amp;ust=1664061588686000&amp;usg=AOvVaw0nAi8NX8IvHqus7AnsektY'>$telefono</a></span></td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='20' style='height:15pt'>
            <td height='20' style='height:15pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='21' style='height:15.75pt'>
            <td height='21' style='height:15.75pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Cliente</td>
            <td style='border-left:none;font-size:12pt;border-top:0.5pt solid windowtext;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>$cliente</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Email</td>
            <td style='color:rgb(5,99,193);text-decoration-line:underline;padding-top:1px;padding-right:1px;padding-left:1px;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>$email</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='20' style='height:15pt'>
            <td height='20' style='height:15pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='25' style='height:18.75pt'>
            <td height='25' style='height:18.75pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Contacto p/ negociar</td>
            <td style='border-left:none;font-size:12pt;border-top:0.5pt solid windowtext;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>$contacto</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='font-size:14pt;font-weight:700;border:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>AUTORIZAR</td>
            <td style='border-left:none;font-size:14pt;font-weight:700;text-align:center;border-top:0.5pt solid windowtext;border-right:0.5pt solid windowtext;border-bottom:0.5pt solid windowtext;background:rgb(146,208,80);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>$enviarorden</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='20' style='height:15pt'>
            <td height='20' style='height:15pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='19' style='height:14.45pt'>
            <td height='19' style='height:14.45pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='font-weight:700;border-top:0.5pt solid windowtext;border-right:none;border-bottom:0.5pt solid windowtext;border-left:0.5pt solid windowtext;background:rgb(242,242,242);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>Observaciones</td>
            <td colspan='4' rowspan='3' width='547' style='border-width:0.5pt;border-style:solid;border-color:windowtext black black windowtext;width:411pt;font-size:12pt;font-weight:700;text-align:center;vertical-align:top;padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-family:Calibri,sans-serif'>$comentariosparaip</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='19' style='height:14.45pt'>
            <td height='19' style='height:14.45pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='19' style='height:14.45pt'>
            <td height='19' style='height:14.45pt;border-top:none;border-right:none;border-bottom:none;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;border:none;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:none;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
           <tr height='21' style='height:15.75pt'>
            <td height='21' style='height:15.75pt;border-top:none;border-right:none;border-bottom:1pt solid windowtext;border-left:1pt solid windowtext;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:none;border-bottom:1pt solid windowtext;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:none;border-bottom:1pt solid windowtext;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:none;border-bottom:1pt solid windowtext;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:none;border-bottom:1pt solid windowtext;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:none;border-bottom:1pt solid windowtext;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
            <td style='border-top:none;border-right:1pt solid windowtext;border-bottom:1pt solid windowtext;border-left:none;background:rgb(255,230,153);padding-top:1px;padding-right:1px;padding-left:1px;color:black;font-size:11pt;font-family:Calibri,sans-serif;vertical-align:bottom;white-space:nowrap'>&nbsp;</td>
           </tr>
          
          </tbody>
          
          <br><hr><h1 style='margin-top:100px;'>Ingrese al siguiente link para completar la tasacion:<a href=$link>$link</h1></a><img src='https://siniestrosdag.com/cover/$imagen' title='mi_titulo' style='height: 900px;width:600px'><hr>";
    
            @require '/home/u277224827/domains/siniestrosdag.com/dash_roles test/vendor/autoload.php';
            @require '/home/u277224827/domains/siniestrosdag.com/dash_roles test/vendor/phpmailer/phpmailer/src/Exception.php';
            @require '/home/u277224827/domains/siniestrosdag.com/dash_roles test/vendor/phpmailer/phpmailer/src/PHPMailer.php';
            @require '/home/u277224827/domains/siniestrosdag.com/dash_roles test/vendor/phpmailer/phpmailer/src/SMTP.php';
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;  
            $mail->Username = 'coordinaciones.dag@gmail.com';
            $mail->Password = 'hvmhibwglsibihzu';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
    
            $mail->setFrom('derivaciones@siniestrosdag.com', 'Coordinaciones - Estudio DAG');
            $mail->addAddress('arcichsilvio@gmail.com');
            
    
    
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $cuerpodelmail;
            // $mail->AddEmbeddedImage($imagen, 'imagen');
            $dt = $mail->Send();
    
                    if($dt){
    
                    echo 'Correo enviado';
                    } else{
                        echo 'Mensaje '. $mail->ErrorInfo;
                    }
            
    
            }
}


