
    
    $(document).ready(function() {
    $('.productores').DataTable({
        pageLength : 15,
        pagingType: "simple",
        lengthChange: false,
        responsive: true,
        processing: true,
        
    
        
    });
    })
    


    
    $(document).ready(function() {
    $('.talleres').DataTable({
        pageLength : 10,
        pagingType: "simple",
        lengthChange: false,
        responsive: true,
        processing: true,

        
    });
})


   function selectedRow(){
                
                var index,
                    table = document.getElementById("productores");
            
                for(var i = 1; i < table.rows.length; i++)
                {
                    table.rows[i].onclick = function()
                    {
                         
                        {
                            $(this).addClass('selected').siblings().removeClass('selected')
                        }
                    };
                }
                
            }
            selectedRow();

            // function selectedRow2(){
                
            //     var index,
            //         table = document.getElementById("inspectores");
            
            //     for(var i = 1; i < table.rows.length; i++)
            //     {
            //         table.rows[i].onclick = function()
            //         {
                         
            //             {
            //                 $(this).addClass('selected').siblings().removeClass('selected')
            //             }
            //         };
            //     }
                
            // }
            // selectedRow2();

            function selectedRow3(){
                
                var index,
                    table = document.getElementById("talleres");
            
                for(var i = 1; i < table.rows.length; i++)
                {
                    table.rows[i].onclick = function()
                    {
                         
                        {
                            $(this).addClass('selected').siblings().removeClass('selected')
                        }
                    };
                }
                
            }
            selectedRow3();




    function clearData(){
    $('#claim_number').val('');
    $('#inspection_date').val('');
    $('#inspector').val('');
    $('#city').val('');
    $('#address').val('');
    $('#email').val('');
    $('#nameError').text('');
    $('#titleError').text('');
    $('#instituteError').text('');
    }

    function reladData(){
        setTimeout(function() {
    location.reload();
    }); 
    }




        function editData(id){
        



    
        $.ajax({
            type:"get",
            dataType:"json",
            url:"/claims/" + id + "/data",
            success: function(data){
                $('#id').val(data.id);
                $('#claim_number').val(data.claim_number);
                $('#inspection_date').val(data.inspection_date);
                $('#license_plate').val(data.license_plate);
                console.log(data);
            }
        })
    }



    function productorData(id){
        



    
        $.ajax({
            type:"get",
            dataType:"json",
            url:"/producers/" + id + "/data",
            success: function(data){
            

                // $('#id_inspector').val(data.id);
                $('#producer_id').val(data.id);
            $('#producer_name').val(data.name);
                $('#emailPas').val(data.email);
                
            

                console.log(data);
            }
        })
    }

    function tallerData(id){
        



    
        $.ajax({
            type:"get",
            dataType:"json",
            url:"/workshops/" + id + "/data",
            success: function(data){
            

                // $('#id_inspector').val(data.id);
                $('#workshop_name').val(data.name);
                $('#email').val(data.email);
                
            

                console.log(data);
            }
        })
    }

    function goTo() {
        let link = "/claims/pending";
        window.location.replace(link);
    };


    // --------------------------------------------- Update de registros a la table de BD -----------------------------------------------------

    function updateData(event){

    event.preventDefault();
    var id = $('#id').val();
    var link = $('#link').val();
    var siniestro =  $('#claim_number').val();
    var patente = $('#license_plate').val();
    var nrocorto = $('#short_number').val();
    var cliente = $('#client_type').val();
    var modalidad = $('#inspection_mode').val();
    //  var imagen = $('#image').val();
    var motivo = $('#reason').val();
    var correo = $('#email').val();
    var observaciones = $('#notes').val();
    var email = $('#email').val();
    var nombretaller = $('#workshop_name').val();
    var telefonos = $('#phone').val();
    var direccion = $('#address').val();
    var localidad = $('#city').val();
    var estado = $('#status').val();
    var fechaip = $('#inspection_date').val();
    var enviarorden = $('#send_order').val();
    var horario = $('#inspection_time').val();
    var comentariosparaip = $('#inspector_notes').val();
    var comentariosdelperitovisita = $('#comentariosdelperitovisita').val();
    var comentariosdelperitofinales = $('#comentariosdelperitofinales').val();
    
    
    
    
    
    
    
    
    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })


    $.ajax({
        type: "PUT",
        
        data: {producer_id: $('#producer_id').val(), inspection_mode:modalidad, reason:motivo, inspection_date:fechaip, license_plate:patente, claim_number:siniestro, city:localidad, address:direccion, email:email, status:estado,
        notes:observaciones, workshop_name:nombretaller, phone:telefonos, city:localidad, send_order:enviarorden, inspection_time:horario, inspector_notes:comentariosparaip, link:link,
        short_number:nrocorto, client_type:cliente, comentariosdelperitovisita:comentariosdelperitovisita, comentariosdelperitofinales:comentariosdelperitofinales},
        url: "/claims/" + id + "/data",
        success: function(data){
                Swal.fire({
                icon: 'success',
                position: 'top-end',
                showConfirmButton: false,
                title: 'Siniestro ingresado con éxito',
            });
                setTimeout(goTo,1500);
        
        
            
        
        console.log('Siniestro asignado con éxito');
        },
    
    })


    }
    // <-------------------------------------- Para enviar correo ---------------------------------------------------------------------------------->

    function Correo(event){

        event.preventDefault();
        // var id = $('#id').val();
        var siniestro =  $('#claim_number').val();
        var emailPas = $('#emailPas').val();
        var coordinador = $('#coordinator').val();
        var patente = $('#license_plate').val();
        
        $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        })


        $.ajax({
            type: "POST",
            
            data: {claim_number:siniestro, emailPas:emailPas, license_plate:patente, coordinator:coordinador},
            url: "/coordination/emails",
            success: function(response){
                    
                Swal.fire({
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: false,
                    title: 'Correo enviado con éxito',
                })
                timer: 500;
                
                
                
                console.log('Correo enviado con éxito');
                }
        
        })


    }

    // <-------------------------------------- Para enviar correo a Edu ---------------------------------------------------------------------------------->

    function CorreoEdu(event){

        event.preventDefault();
        // var id = $('#id').val();
        var siniestro =  $('#claim_number').val();
        var email = $('#email').val();
        var fechaip = $('#inspection_date').val();
        var patente = $('#license_plate').val();
        var nrocorto =  $('#short_number').val();
        var comentariosparaip = $('#inspector_notes').val();
        var telefono = $('#phone').val();
        var localidad = $('#city').val();
        var direccion =  $('#address').val();
        var modalidad = $('#inspection_mode').val();
        var link = $('#link').val();
        // var nombretaller = $('#workshop_name').val();
        var motivo = $('#reason').val();
        var horario = $('#inspection_time').val();
        var cliente = $('#client_type').val();
        var enviarorden = $('#send_order').val();
        var imagen = $('#image').val();
        var contacto = $('#contact').val();
        var lugar = $('#location').val();





        
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })


        $.ajax({
            type: "POST",
            
            data: {claim_number:siniestro, email:email, inspection_date:fechaip, license_plate:patente, short_number:nrocorto, inspector_notes:comentariosparaip, phone:telefono, city:localidad, address:direccion,
            inspection_mode:modalidad, reason:motivo, inspection_time:horario, send_order:enviarorden, client_type:cliente, link:link, image:imagen, location:lugar, contact:contacto},
            url: "/coordination/inspection-emails",
            success: function(response){
                    
                Swal.fire({
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: false,
                    title: 'Enviado a Edu con exito',
                })
                timer: 500;
                
                
                
                console.log('Correo enviado con exito');
                }
        
        })


    }

 

