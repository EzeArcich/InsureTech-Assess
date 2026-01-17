
    
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
    $('#siniestro').val('');
    $('#fechaip').val('');
    $('#inspector').val('');
    $('#localidad').val('');
    $('#direccion').val('');
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
            url:"/teacher/edit/"+id,
            success: function(data){
                $('#id').val(data.id);
                $('#siniestro').val(data.siniestro);
                $('#fechaip').val(data.fechaip);
                $('#patente').val(data.patente);
                $('#direccion').val(data.direccion);
                $('#localidad').val(data.localidad);
                console.log(data);
            }
        })
    }



    function productorData(id){
        



    
        $.ajax({
            type:"get",
            dataType:"json",
            url:"/teacher/productores/"+id,
            success: function(data){
            

                // $('#id_inspector').val(data.id);
                $('#nombre').val(data.nombre);
                $('#emailPas').val(data.correo);
                
            

                console.log(data);
            }
        })
    }

    function tallerData(id){
        



    
        $.ajax({
            type:"get",
            dataType:"json",
            url:"/teacher/taller/"+id,
            success: function(data){
            

                // $('#id_inspector').val(data.id);
                $('#nombretaller').val(data.taller);
                $('#telefono').val(data.telefonos);
                $('#email').val(data.email);
                $('#direccion').val(data.direccion);
                $('#localidad').val(data.localidad);
                
            

                console.log(data);
            }
        })
    }

    function goTo() {
        let link = "https://InsureTechAsses.com/siniestros/pendientes";
        window.location.replace(link);
    };


    // --------------------------------------------- Update de registros a la table de BD -----------------------------------------------------

    function updateData(event){

    event.preventDefault();
    var id = $('#id').val();
    var link = $('#link').val();
    var siniestro =  $('#siniestro').val();
    var patente = $('#patente').val();
    var nrocorto = $('#nrocorto').val();
    var cliente = $('#cliente').val();
    var modalidad = $('#modalidad').val();
    //  var imagen = $('#imagen').val();
    var motivo = $('#motivo').val();
    var correo = $('#correo').val();
    var observaciones = $('#observaciones').val();
    var email = $('#email').val();
    var nombretaller = $('#nombretaller').val();
    var telefonos = $('#telefono').val();
    var direccion = $('#direccion').val();
    var localidad = $('#localidad').val();
    var estado = $('#estado').val();
    var fechaip = $('#fechaip').val();
    var enviarorden = $('#enviarorden').val();
    var horario = $('#horario').val();
    var comentariosparaip = $('#comentariosparaip').val();
    var comentariosdelperitovisita = $('#comentariosdelperitovisita').val();
    var comentariosdelperitofinales = $('#comentariosdelperitofinales').val();
    
    
    
    
    
    
    
    
    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })


    $.ajax({
        type: "PUT",
        
        data: {modalidad:modalidad, motivo:motivo, fechaip:fechaip, patente:patente, siniestro:siniestro, localidad:localidad, direccion:direccion, email:email, estado:estado,
        observaciones:observaciones, nombretaller:nombretaller, telefono:telefonos, localidad:localidad, enviarorden:enviarorden, horario:horario, comentariosparaip:comentariosparaip, link:link,
        nrocorto:nrocorto, cliente:cliente, comentariosdelperitovisita:comentariosdelperitovisita, comentariosdelperitofinales:comentariosdelperitofinales},
        url: "/teacher/update/"+id,
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
        var siniestro =  $('#siniestro').val();
        var emailPas = $('#emailPas').val();
        var coordinador = $('#coordinador').val();
        var patente = $('#patente').val();
        
        $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        })


        $.ajax({
            type: "POST",
            
            data: {siniestro:siniestro, emailPas:emailPas, patente:patente, coordinador:coordinador},
            url: "/correo",
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
        var siniestro =  $('#siniestro').val();
        var email = $('#email').val();
        var fechaip = $('#fechaip').val();
        var patente = $('#patente').val();
        var nrocorto =  $('#nrocorto').val();
        var comentariosparaip = $('#comentariosparaip').val();
        var telefono = $('#telefono').val();
        var localidad = $('#localidad').val();
        var direccion =  $('#direccion').val();
        var modalidad = $('#modalidad').val();
        var link = $('#link').val();
        // var nombretaller = $('#nombretaller').val();
        var motivo = $('#motivo').val();
        var horario = $('#horario').val();
        var cliente = $('#cliente').val();
        var enviarorden = $('#enviarorden').val();
        var imagen = $('#imagen').val();
        var contacto = $('#contacto').val();
        var lugar = $('#lugar').val();





        
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })


        $.ajax({
            type: "POST",
            
            data: {siniestro:siniestro, email:email, fechaip:fechaip, patente:patente, nrocorto:nrocorto, comentariosparaip:comentariosparaip, telefono:telefono, localidad:localidad, direccion:direccion,
            modalidad:modalidad, motivo:motivo, horario:horario, enviarorden:enviarorden, cliente:cliente, link:link, imagen:imagen, lugar:lugar, contacto:contacto},
            url: "/correoEdu",
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

 

