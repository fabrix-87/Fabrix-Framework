var user_id = 0;

$(function () {
	var user_table = $("#user_list").DataTable({
		ajax: {
			url: 'api/admin/users/all',
			dataSrc: 'user_list'
		},
		columns: [
			{ data: 'user_id' },
			{ data: 'firstname' },
			{ data: 'lastname' },
			{ data: 'username' },
			{ data: 'registration_date' },
			{ data: 'email' },
			{ data: null }
		],
		columnDefs: [ {
    		"targets": -1,
    		"data": "user_id",
    		"render": function ( data, type, row, meta ) {
				console.log(data);
      			return '<a class="btn btn-primary btn-sm" title="Modifica" user_id="'+data.user_id+'" href="#"><i class="fas fa-user-edit"></i></a> <a class="btnRemoveUser btn btn-danger btn-sm" title="Elimina" data-target="#deleteUser" data-toggle="modal" href="#" user_id="'+data.user_id+'"><i class="fas fa-user-times"></i></a>';
    		}
  		} ],
		language: {
			"url": "assets/plugins/datatables/Italian.json"
		}
	});

	$('#userForm').on('submit', function (e) {
		if (e.isDefaultPrevented()) {
			return false;
		} else {
			$.post('ajax.php?route=users&action=addUser',
			$('#userForm').serialize(),
			function(data, status, xhr){
				window.location.reload();
			});
			return false;
		}
	});


	$("#btnFormSubmit").click(function(e){
		e.preventDefault();
		$('#userForm').submit();
	});

	// Fill modal with content from link href
	$("#userDetailsModal").on("show.bs.modal", function(e) {
		var link = $(e.relatedTarget);
		$(this).find(".modal-body").load(link.attr("href"));
	});

	/**
	*  Elimina utente
	*/
	$('.btnRemoveUser').on('click', function(){
		user_id = $(this).attr('user_id');
	});
	$('#btn_confirm_delete_user').click(function(){
		$.post('ajax.php?route=users&action=deleteUser',
		{ 'user_id' : user_id },
		function(data, status, xhr){
			user_table
			.row('#user_'+user_id)
			.remove()
			.draw();
			$('#alert-success').find('span').html('Utente eliminato');
			$('#alert-success').slideDown('slow').delay(2000).slideUp('slow');
		});
		$('#deleteUser').modal('hide');
	});

});
