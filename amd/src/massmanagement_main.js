// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/massmanagement_main
  */

define(['jquery','block_ases/bootstrap','block_ases/datatables.net','block_ases/datatables.net-buttons','block_ases/buttons.flash','block_ases/jszip','block_ases/pdfmake','block_ases/buttons.html5','block_ases/buttons.print','block_ases/sweetalert','block_ases/select2'], function($,bootstrap,datatables,sweetalert,select2) {


  return {
      init: function() {
    
    var val = $('#selector').val();
    addHelpMessage(val);
     $('#selector').on('change', function () {
        var val = $('#selector').val();
        addHelpMessage(val);
     });
     
    $('#boton_subir').on('click', function(){
        $('#informacion').empty();
        uploadFile();
    });

function uploadFile() {
    
    var urlParameters =  getUrlParams(document.location.search); //metodo definido en checrole
    
    var formData = new FormData();
    
    formData.append('idinstancia',urlParameters.instanceid);
    formData.append('file', $('#archivo')[0].files[0]);
    
    var controler = '';
    
    switch ($('#selector').val()) {
        case 'seguimiento':
            controler = 'mrm_seguimiento.php';
            break;
        case 'seguimiento_estudiante':
            controler = 'mrm_seg_estud.php'; //msm_monitor_estud
            break;
        case 'monitor_estud':
            controler = 'mrm_monitor_estud.php'; //
            break;
        case 'roles_usuario':
            controler = 'mrm_roles.php'; //
            break;
        default:
            return 0;
    }

    $.ajax({
        url: '../managers/mass_management/'+controler,
        data: formData,
        type: 'POST',
        dataType: 'json',
        cache: false,
        // parametros necesarios para la carga de archivos
        contentType: false,
        processData: false,
        beforeSend: function() {
            $('#response').html("<img src='../icon/facebook.gif' />");
        },
        success : function (msj) {

             $('#response').empty();
            
            $('#informacion').empty();
            
            if(msj.success){
                $('#informacion').append('<div class="alert alert-success"><h4 align="center">Información</h4><strong>Exito!</strong> <br><p>'+msj.success+'</p></div>');
            }else if(msj.warning){
                $('#informacion').append('<div class="alert alert-warning"><h4 align="center">Información</h4><strong>Cargado con inconsitencias!</strong> <br>'+msj.warning+'</div>');
            }else if(msj.error){
                $('#informacion').append('<div class="alert alert-danger"><h4 align="center">Información</h4><strong>Error!</strong> <br>'+msj.error+'</div>');
            }
            
            $('#informacion').append(msj.urlzip);
        },
        error: function (msj) {
            alert("error");
        }
        // ... Other options like success and etc
    });
     
}
    function addHelpMessage(selector){
    $('#informacion').empty();
    switch (selector) {
        case 'seguimiento':
            $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>registroid</li><li>email_monitor ó username</li><li>created</li><li>hora_ini</li><li>hora_fin</li><li>tema</li><li>objetivos</li><li>tipo</li> </ul> </p><p>Columnas aceptadas: <ul> <li>fecha</li> <li>actividades</li><li>observaciones</li><li>familiar_desc</li><li>familiar_riesgo</li> <li>academico</li> <li>academico_riesgo</li> <li>economico</li> <li>economico_riesgo</li> <li>vida_uni</li> <li>vida_uni_riesgo</li> <li>individual</li> <li>individual_riesgo</li> </ul> </p></div>');
            break;
        case 'seguimiento_estudiante':
            $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>seguimientoid</li><li>username</li> </ul> </p></div>');
            break;
        case 'monitor_estud':
            $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username_monitor</li><li>username_estudiante</li> </ul> </p></div>');
            break;
        case 'roles_usuario':
            $('#informacion').append('<div class="alert alert-info"><h4 align="center">Información</h4><strong>Para tener en cuenta...</strong> <br><p>Columnas obligatorias:<ul> <li>username</li><li>rol(administrativo, reportes,profesional_ps, monitor_ps,  estudiante_t ó practicante_psp)</li> </ul> </p><p>Columnas aceptadas: <ul> <li>jefe</li>  </ul> </p></div>');
            break;
        
        default:
            // code
    }
}


       }
    };
});