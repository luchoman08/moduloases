'use strict';define(['jquery','block_ases/bootstrap','block_ases/sweetalert','block_ases/jqueryui','block_ases/select2'],function(a){return{init:function init(){function f(g){for(var h=g.indexOf('?'),j='',k=h;k<g.length;k++)j+=g[k];return j}a('#dphpforms-redirect-new-form').click(function(){window.location.href='dphpforms_form_builder.php'+f(window.location.href)}),a('#dphpforms-redirect-adm-alias').click(function(){window.location.href='dphpforms_alias_editor.php'+f(window.location.href)}),a('#dphpforms-redirect-adm-forms').click(function(){window.location.href='dphpforms_form_editor.php'+f(window.location.href)}),a('.btn-remove-form').click(function(){var g=a(this).attr('data-form-name'),h=a(this).attr('data-form-id');swal({html:!0,title:'Confirmaci\xF3n',text:'<strong>Nota importante!</strong>: Est\xE1 eliminando el formulario <strong><i>'+g+'</i></strong>, \xBFdesea continuar?, tenga en consideraci\xF3n que los alias asociados a las preguntas del formulario no ser\xE1n eliminados.',type:'warning',showCancelButton:!0,confirmButtonText:'S\xED, Eliminar!'},function(j){j&&a.get('../managers/dphpforms/dphpforms_form_updater.php?function=delete_form&id_form='+h,function(k){var l=k;0==l.status?swal({title:'Informaci\xF3n',text:'Eliminado',type:'success'},function(){window.location.href=window.location.href}):-1==l.status&&swal('Error!',l.message,'error')})})}),a('.btn-remove-alias').click(function(){var g=a(this).attr('data-form-alias'),h=a(this).attr('data-form-id');swal({html:!0,title:'Confirmaci\xF3n',text:'<strong>Nota importante!</strong>: Est\xE1 eliminando el alias <strong><i>'+g+'</i></strong>, \xBFdesea continuar?, tenga en consideraci\xF3n que cualquier consulta que haga uso de este alias dejar\xE1 de funcionar.',type:'warning',showCancelButton:!0,confirmButtonText:'S\xED, Eliminar!'},function(j){j&&a.get('../managers/dphpforms/dphpforms_form_updater.php?function=delete_alias&id_alias='+h,function(k){var l=k;0==l.status?swal({title:'Informaci\xF3n',text:'Eliminado',type:'success'},function(){window.location.href=window.location.href}):-1==l.status&&swal('Error!',l.message,'error')})})})}}});