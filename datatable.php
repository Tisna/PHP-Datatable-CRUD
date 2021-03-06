<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>PHP Datatable</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
</head>
<body>
	<div class="container box">
   		<div class="table-responsive">
	    	<div align="right">
		     	<button type="button" id="add_button" data-toggle="modal" data-target="#userModal" class="btn btn-info">Add</button>
		     	<hr>
	    	</div>
	    	<table id="user_data" class="table table-bordered table-striped">
	     		<thead>
	      			<tr>
				       <th width="10%">Image</th>
				       <th width="35%">First Name</th>
				       <th width="35%">Last Name</th>
				       <th width="10%">Edit</th>
				       <th width="10%">Delete</th>
	      			</tr>
	     		</thead>
	    	</table>
	   </div>
  </div>

	<div id="userModal" class="modal fade">
 		<div class="modal-dialog">
  			<form method="post" id="user_form" enctype="multipart/form-data">
   				<div class="modal-content">
    				<div class="modal-header">
     					<button type="button" class="close" data-dismiss="modal">&times;</button>
    				</div>
    				<div class="modal-body">
     					<label>Enter First Name</label>
     					<input type="text" name="first_name" id="first_name" class="form-control" />
					    <br />
					    <label>Enter Last Name</label>
					    <input type="text" name="last_name" id="last_name" class="form-control" />
					    <br />
					    <label>Select User Image</label>
					    <input type="file" name="user_image" id="user_image" />
					    <span id="user_uploaded_image"></span>
    				</div>
				    <div class="modal-footer">
					    <input type="hidden" name="user_id" id="user_id" />
					    <input type="hidden" name="operation" id="operation" />
					    <input type="submit" name="action" id="action" class="btn btn-success" value="Add" />
					    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				    </div>
   				</div>
  			</form>
 		</div>
	</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		$('#add_button').click(function(){
			$('#user_form')[0].reset();
			$('.modal-title').text("Add User");
			$('#action').val("Add");
			$('#operation').val("Add");
			$('#user_uploaded_image').html('');
		});
		var dataTable = $('#user_data').DataTable({
			"processing":true,
			"serverSide":true,
			"order":[],
			"ajax":{
				url:"fetch.php",
				type:"POST"
			},
			"columnDefs":[
			{
				"targets":[0, 3, 4],
				"orderable":false,
			},
			],
		});
		$(document).on('submit', '#user_form', function(event){
			event.preventDefault();
			var firstName = $('#first_name').val();
			var lastName = $('#last_name').val();
			var extension = $('#user_image').val().split('.').pop().toLowerCase();
			if(extension != ''){
				if(jQuery.inArray(extension, ['gif','png','jpg','jpeg']) == -1){
					alert("Invalid Image File");
					$('#user_image').val('');
					return false;
			   }
			} 
			if(firstName != '' && lastName != ''){
				$.ajax({
				    url:"insert.php",
				    method:'POST',
				    data:new FormData(this),
				    contentType:false,
				    processData:false,
				    success:function(data){
					    alert(data);
					    $('#user_form')[0].reset();
					    $('#userModal').modal('hide');
					    dataTable.ajax.reload();
				    }
			   });
			}else{
				alert("Both Fields are Required");
			}
		});
		$(document).on('click', '.update', function(){
			var user_id = $(this).attr("id");
			$.ajax({
				url:"fetch_single.php",
				method:"POST",
				data:{user_id:user_id},
				dataType:"json",
				success:function(data){
				    $('#userModal').modal('show');
				    $('#first_name').val(data.first_name);
				    $('#last_name').val(data.last_name);
				    $('.modal-title').text("Edit User");
				    $('#user_id').val(user_id);
				    $('#user_uploaded_image').html(data.user_image);
				    $('#action').val("Edit");
				    $('#operation').val("Edit");
				}
		  	})
		});
		$(document).on('click', '.delete', function(){
			var user_id = $(this).attr("id");
			if(confirm("Are you sure you want to delete this?")){
				$.ajax({
					url:"delete.php",
					method:"POST",
					data:{user_id:user_id},
					success:function(data){
					    alert(data);
					    dataTable.ajax.reload();
					}
			   });
		  	}else{
		  		return false; 
		  	}
		});
	});
</script>
</body>
</html>