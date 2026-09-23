$(document).ready(function() {
    $(function() {
        if( $('#userLogDatePicker').length ){     
            $('#userLogDatePicker').datetimepicker({      
                viewMode : 'months',
                format : 'MMM-YYYY',
                toolbarPlacement: "top"   
            }).datetimepicker("setDate", new Date());

            $('#userLogDatePicker').data("DateTimePicker").date(moment().subtract(1, 'days'));
        }

        /* if( $('#DOB').length ){     
            $('#DOB').datetimepicker({ 
                format : 'YYYY-MM-DD',
                toolbarPlacement: "top"              
            }); 
            $('#DOB').data("DateTimePicker").date(moment().subtract(1, 'days'));           
        } */		
		
		var swalMessage = [];
		swalMessage = {
			"trash":[
				{"title":"Are you sure you want to trash this user?"}, 
				{"text":"If you trash this, it will be moved to trashed folder."}
			],
			"delete":[
				{"title":"Are you sure you want to delete this user?"}, 
				{"text":"If you delete this, it will be gone forever."}
			],
			"restore":[
				{"title":"Are you sure you want to restore this user?"}, 
				{"text":"Please confirm restore."}
			],
		};
		
		$('.show_confirm_delete').click(function(event) {
			  var form =  $(this).closest("form");
			  var method = $(this).data("method");
			  event.preventDefault();			  
			  var msg = swalMessage[method];			  
			  swal({
				  title: msg[0]['title'],
				  text: msg[1]['text'],
				  icon: "warning",
				  buttons: true,
				  dangerMode: true,
			  })
			  .then((isConfirmed) => {
				if (isConfirmed) {
				  form.submit();
				}
			  });
		});		
	
		// Draw #Spins table
		let spinstable = new DataTable('#spins-table', {
			search: {
				return: true
			},
			responsive: true,
			pageLength: 25
		});	

		// #Spins table search
		$('#spins-table_wrapper #dt-search-0').on('keyup', function () {
			spinstable.search(this.value).draw();
		});
	});
});