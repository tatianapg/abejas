jQuery(document).ready(function(){

$(".goo-collapsible > li > a").on("click", function(e){
      
	if(!$(this).hasClass("active")) {
		
      // hide any open menus and remove all other classes
		$(".goo-collapsible li ul").slideUp(350);
		$(".goo-collapsible li a").removeClass("active");
      
		// open our new menu and add the open class
		$(this).next("ul").slideDown(350);
		$(this).addClass("active");
		
	}else if($(this).hasClass("active")) {
		
		$(this).removeClass("active");
		$(this).next("ul").slideUp(350);
	}
});

});

function loadQueryResults(forma) {
	$('#ladoDerecho').load(forma);
    return false;
  
};

function cargarResultadosDivTerapias(valor) {
	if(valor) {
		$.get("frmListaTerapias.php", { cdtra: valor},
		   function(data) {
			 $('#divResultadosTerapias').html(data);
		   });
	}   
}


function cargarResultadosDivMedicaciones(valor) {
	if(valor) {
		$.get("frmListaMedicaciones.php", { cdtra: valor},
		   function(data) {
			 $('#divResultadosMedicaciones').html(data);
		   });
	}   
}


function cargarResultadosDivPacientes() {
    //alert("Entrando a la funcio de div");
	var campo = $("#cmbCam").val();
	var apellidos = $("#txtApe").val();
	if(apellidos.length>0) {
		$.post("buscarPaciente.php", { txtApe: apellidos, cmbCam: campo},
		   function(data) {
			 $('#divResultadosBusquedaPaciente').html(data);
			 $('#frmBuscarPaciente')[0].reset();
		   });
	}   
}


function cargarPacientesPaginacion(txtApe, cmbCam, pagina) {    
	var apellidos = txtApe;
	if(apellidos.length>0) {
		$.post("buscarPaciente.php", { txtApe: apellidos, cmbCam: cmbCam, pag: pagina},
		   function(data) {
			 $('#divResultadosBusquedaPaciente').html(data);
			 //$('#frmBuscarPaciente')[0].reset();
		   });
	}   
}


function cargarResultadosDivProductos() {
	var producto = $("#txtPro").val();
	var criterio = $("#cmbCriterio").val();
	
	if(producto.length>0) {
		$.post("buscarProducto.php", { txtPro: producto, cmbCriterio : criterio},
		   function(data) {
			 $('#divResultadosBusquedaProducto').html(data);
			 $('#frmBuscarProducto')[0].reset();
		   });
	}   
}

function cargarProductosPaginacion(txtPro, pagina) {
	var producto = txtPro;
	
	if(producto.length>0) {
		$.post("buscarProducto.php", { txtPro: producto, pag: pagina},
		   function(data) {
			 $('#divResultadosBusquedaProducto').html(data);
			 //$('#frmBuscarProducto')[0].reset();
		   });
	}   
}


function cargarDivNombreProducto() {
	var sku = $("#txtCodigoProducto").val();
	if(sku.length>0) {
		$.post("cargarNombreProducto.php", { txtCodigoProducto: sku},
		   function(data) {
			 $('#divNombreProducto').html(data);
			 $('#divResultadosCargaProducto').html('');			 
			 //$('#txtCodigoProducto').html(codigo);
			 //$('#frmCargarProducto')[0].reset();
		   });
	}   

}


function cargarResultadosDivCargarProducto() {

	var codigo = $("#txtCodigoProducto").val();
	var cantidad = $("#txtCantidadAccion").val();
	//var sucursal = $("#cmbSucursal").val();
	var subtipo = $("#cmbSubtipo").val();
	var accion = $("#txtTipoAccion").val();
	var inicial = $("#cmbInicial").val();
	if(codigo.length>0 && cantidad >0 && accion > 0) {
		$.post("cargarProducto.php", { txtCodigoProducto: codigo, txtCantidadAccion: cantidad, txtTipoAccion: accion, cmbInicial: inicial, cmbSubtipo: subtipo },
		   function(data) {
			 $('#divResultadosCargaProducto').html(data);			 
			 $('#divNombreProducto').html('');			 
			 $('#frmCargarProducto')[0].reset();
		   });
	}   

}

//funcion que permite cargar los resultados de la consulta de inventarios
function cargarResultadosDivInventarios() {
	var anio = $("#txtAnioBuscar").val();

	if(anio.length>0 && anio > 0) {
		$.post("buscarInventario.php", { txtAnioBuscar: anio },
		   function(data) {
			 $('#divResultadosBusquedaInventario').html(data);			 
			 $('#frmBuscarInventario')[0].reset();
		   });
	}   
}



//nuevas funciones para cargar datos de la venta
function cargarResultadosDivVenta(limpiar) {

		//alert('limpiar ' + limpiar);
		var producto = $("#txtCodigoProducto").val();
		if(producto.length>0 && limpiar == 1) {
			$.post("ventaProducto.php", { txtCodigoProducto: producto, limpiar : limpiar},
			   function(data) {
				 $('#divVenta').html(data);
				 $('#frmVentaProducto')[0].reset();
				 $('#txtCodigoProducto').focus();
			   });
		}
		if(limpiar == -1) {
			if(confirm('¿Cancelar esta venta y guardar auditoría?') == true) {
				$.post("ventaProducto.php", { txtCodigoProducto: producto, limpiar : limpiar},
			    function(data) {
					$('#divVenta').html(data);
					$('#frmVentaProducto')[0].reset();
					$('#txtCodigoProducto').focus();
				});
				
			}  //fin confirm
		}
			 
}	

function grabarVenta() {
	
	var error = $("#bndErr").val();
	if(error != 1) {
	
		if(confirm('¿Grabar la venta y generar recibo?') == true) {
			var cliente = $("#txtCliente").val();
			var subtotal = $("#txtSubtotal").val();
			var numItems = $("#txtItems").val();
			$.post("grabarVenta.php", { grabarVenta : 1, txtCliente : cliente, txtSubtotal : subtotal, txtItems : numItems},
			   function(data) {
				 $('#divVenta').html(data);
				 $('#frmVentaProducto')[0].reset();
				 $('#txtDescuento').val(0.0);
				 $('#txtCliente').val('Consumidor final');			 
			   });
		} else {
			$('#txtCodigoProducto').focus();
		}  
	} else {
		alert('Existe error en el descuento, revise.');	
	}
}


/* permite ingresar un descuento*/
function ingresarDescuento() {
	var descuento = $("#txtDescuento").val();
	if(descuento.length>0) {
		$.post("ventaProducto.php", { txtDescuento: descuento, limpiar : 2},
		  function(data) {
			 $('#divVenta').html(data);
			 $('#frmVentaProducto')[0].reset();
			 $('#txtDescuento').val(0.0);
			 $('#txtCodigoProducto').focus();
		  });
	}	
}

function borrarItem(codItem) {
	$.post("ventaProducto.php", { cdIndice: codItem, limpiar : 3},
	function(data) {
		$('#divVenta').html(data);
		$('#frmVentaProducto')[0].reset();
		$('#txtCodigoProducto').focus();
	});
}	


/* funcion para cargar resulltados de la búsqueda de un recibo*/
function cargarResultadosDivRecibo() {
	var recibo = $("#txtCdRecibo").val();
	
	if(recibo.length>0) {
		$.post("consultarVenta.php", { txtCdRecibo: recibo},
		   function(data) {
			 $('#divResultadosRecibo').html(data);
			 $('#frmConsultarVenta')[0].reset();
		   });
	}   
}
