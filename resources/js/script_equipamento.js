 function Salvar(){
      var nome = $("#nome");
      var serie = $("#serie");
      var modelo = $("#modelo");
      var filial = $("#filial");
      if (nome.val()!="" && serie.val()!="" && modelo.val()!="" && filial.val()!="") {
         $.ajax({ 
                    url: 'class/DaoEquipamento.php', 
                    type: 'POST', 
                    data:{"nome" : nome.val(),"serie":serie.val(),"modelo":modelo.val(),"filial":filial.val()}, 
                    success: function(data) {                
                    data = $.parseJSON(data); 
                    console.log(data);
                    if (data.label=="Certo") {
                        $('#nome').attr('disabled','true');
                        $('#serie').attr('disabled','true');
                        $('#modelo').attr('disabled','true');
                        $('#filial').attr('disabled','true');
                        $("#btn").text("Novo");
                        $('#btn').attr('onclick','window.location="equipamento.php";');
                        getDados(data.id);
                        alert("Salvo com Sucesso!");
                    }else{
                      alert("Erro ao Inserir!");
                    }
                    
                  }
          }); 
     }else{
      alert("Preencha Todos os Campos!");
     }
}
 function Buscar($id){

      var id =$id;
      var buscar = 'ok';
         $.ajax({ 
                url: 'class/DaoEquipamento.php', 
                type: 'POST', 
                data:{"id" : id,"buscar" : buscar}, 
                success: function(data) {                
                data = $.parseJSON(data); 
                if (data.label=="Certo") {
                  $('#nome').val(data.nome);
                  $('#serie').val(data.serie);
                  $('#modelo').val(data.marcamodelo);
                  if (data.filial==1) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 1;
                      $('#filial').attr('disabled','true');
                  }else if (data.filial==2) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 2;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==3) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 3;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==5) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 4;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==6) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 5;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==7) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 6;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==8) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 7;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==10) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 8;
                     $('#filial').attr('disabled','true');
                  }

                  $("#btn").text("Alterar");
                  $('#btn').attr('onclick','Alterar('+id+')');
                }else{
                  alert("Erro ao Buscar!");
                }
                console.log(data);
                getDados(id);
              }
          }); 
}
 function Visualizar($id){

      var id =$id;
      var buscar = 'ok';
         $.ajax({ 
                url: 'class/DaoEquipamento.php', 
                type: 'POST', 
                data:{"id" : id,"buscar" : buscar}, 
                success: function(data) {   
                $( "#btn" ).remove();             
                data = $.parseJSON(data); 
                if (data.label=="Certo") {
                  $('#nome').val(data.nome);
                  $('#nome').attr('disabled','true');

                  $('#serie').val(data.serie);
                  $('#serie').attr('disabled','true');

                  $('#modelo').val(data.marcamodelo);
                  $('#modelo').attr('disabled','true');

                  if (data.filial==1) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 1;
                      $('#filial').attr('disabled','true');
                  }else if (data.filial==2) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 2;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==3) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 3;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==5) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 4;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==6) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 5;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==7) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 6;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==8) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 7;
                     $('#filial').attr('disabled','true');
                  }else if (data.filial==10) {
                     var comoFilial = document.getElementById("filial");
                     comoFilial.selectedIndex = 8;
                     $('#filial').attr('disabled','true');
                  }

                  
                }else{
                  alert("Erro ao Buscar!");
                }
                console.log(data);
                getDados(id);
              }
          }); 
}
 function Alterar($id){
      var id = $id;
      var alterar = 'ok';
      var nome = $("#nome");
      var serie = $("#serie");
      var modelo = $("#modelo");
      var filial = $("#filial");
      if (nome.val()!="" && serie.val()!="" && modelo.val()!="" ) {
         $.ajax({ 
                    url: 'class/DaoEquipamento.php', 
                    type: 'POST', 
                    data:{"nome" : nome.val(),"serie":serie.val(),"modelo":modelo.val(),"id" : id,"alterar" : alterar}, 
                    success: function(data) {                
                    data = $.parseJSON(data); 
                    console.log(data);
                    if (data.label=="Certo") {
                        $('#nome').attr('disabled','true');
                        $('#serie').attr('disabled','true');
                        $('#modelo').attr('disabled','true');
                        $('#filial').attr('disabled','true');
                        alert("Alterado com Sucesso!");
                        $(location).attr('href', 'listar_equipamento.php');
                    }else{
                      alert("Erro ao Alterar!");
                    }
                    
                  }
          }); 
     }else{
      alert("Preencha Todos os Campos!");
     }
}
     function CriaRequest() {
     try{
         request = new XMLHttpRequest();        
     }catch (IEAtual){
          
         try{
             request = new ActiveXObject("Msxml2.XMLHTTP");       
         }catch(IEAntigo){
          
             try{
                 request = new ActiveXObject("Microsoft.XMLHTTP");          
             }catch(falha){
                 request = false;
             }
         }
     }
      
     if (!request) 
         alert("Seu Navegador não suporta Ajax!");
     else
         return request;
 };



  function getDados($id) {    
      var id = $id;
      var xmlreq = CriaRequest();
    
     xmlreq.open("GET", "class/gerador_qrcod.php?id="+id, true);
      
   
     xmlreq.onreadystatechange = function(){
          
         
         if (xmlreq.readyState == 4) {
              
             if (xmlreq.status == 200) {
                 result.innerHTML = xmlreq.responseText;
             }else{
                 result.innerHTML = "Erro: " + xmlreq.statusText;
             }
         }
     };
     xmlreq.send(null);
 };



 
      var data = {
      	labels: ["January", "February", "March", "April", "May"],
      	datasets: [
      		{
      			label: "My First dataset",
      			fillColor: "rgba(220,220,220,0.2)",
      			strokeColor: "rgba(220,220,220,1)",
      			pointColor: "rgba(220,220,220,1)",
      			pointStrokeColor: "#fff",
      			pointHighlightFill: "#fff",
      			pointHighlightStroke: "rgba(220,220,220,1)",
      			data: [65, 59, 80, 81, 56]
      		},
      		{
      			label: "My Second dataset",
      			fillColor: "rgba(151,187,205,0.2)",
      			strokeColor: "rgba(151,187,205,1)",
      			pointColor: "rgba(151,187,205,1)",
      			pointStrokeColor: "#fff",
      			pointHighlightFill: "#fff",
      			pointHighlightStroke: "rgba(151,187,205,1)",
      			data: [28, 48, 40, 19, 86]
      		}
      	]
      };
      var pdata = [
      	{
      		value: 300,
      		color: "#46BFBD",
      		highlight: "#5AD3D1",
      		label: "Complete"
      	},
      	{
      		value: 50,
      		color:"#F7464A",
      		highlight: "#FF5A5E",
      		label: "In-Progress"
      	}
      ]
      
     